<?php

declare(strict_types=1);

namespace App\Services\AdminGerai;

use App\Data\AdminGerai\BoothDashboardData;
use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Events\QueueCalled;
use App\Events\QueueCreated;
use App\Events\QueueFinished;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * BoothOperationService
 *
 * Encapsulates all queue-state transitions and data retrieval for an operator booth (Admin Gerai).
 */
final class BoothOperationService
{
    /**
     * Get dashboard variables for the operator booth.
     */
    public function getDashboardData(User $user): ?BoothDashboardData
    {
        if (! $user->departments_id) {
            return null;
        }

        $department = Department::find($user->departments_id);
        if (! $department) {
            return null;
        }

        $today = Carbon::today();

        // Antrean yang sedang aktif dilayani (Serving)
        $activeQueue = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::Serving->value)
            ->with(['user'])
            ->first();

        // Daftar antrean menunggu (Checked-In)
        $waitingQueues = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::CheckedIn->value)
            ->with(['user'])
            ->orderBy('is_priority', 'desc')
            ->orderBy('is_waterfall_forwarded', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        // Daftar antrean terlewat (Skipped)
        $skippedQueues = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::Skipped->value)
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Hitung Metrik
        $remainingCount = $waitingQueues->count();

        // Rata-rata durasi pelayanan (dalam menit)
        $completedToday = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::Completed->value)
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedToday->isEmpty()) {
            $avgServiceTime = 12; // nilai default estimasi
        } else {
            $totalSeconds = $completedToday->sum(fn (Queue $q) => $q->calculateDuration());
            $avgServiceTime = (int) round(($totalSeconds / $completedToday->count()) / 60);
            $avgServiceTime = max($avgServiceTime, 1);
        }

        // Active departments for forward modal (exclude own department)
        $activeDepartments = Department::where('is_open', true)
            ->where('id', '!=', $department->id)
            ->orderBy('nomor_loket')
            ->get(['id', 'name', 'inisial', 'nomor_loket']);

        return new BoothDashboardData(
            department: $department,
            activeQueue: $activeQueue,
            waitingQueues: $waitingQueues,
            skippedQueues: $skippedQueues,
            remainingCount: $remainingCount,
            avgServiceTime: $avgServiceTime,
            activeDepartments: $activeDepartments
        );
    }

    /**
     * Update status operasional loket.
     */
    public function updateStatus(User $user, string $status): void
    {
        $department = Department::findOrFail($user->departments_id);

        Cache::put("loket_status_{$department->id}", $status, now()->addDay());

        ActivityLog::record(
            action: 'UPDATE_COUNTER_STATUS',
            modelType: 'Department',
            modelId: $department->id,
            description: "Operator mengubah status loket instansi '{$department->name}' menjadi '{$status}'.",
            actorUserId: $user->id
        );
    }

    /**
     * Toggle status operasional instansi (buka/tutup).
     */
    public function toggleDepartmentStatus(User $user): bool
    {
        $department = Department::findOrFail($user->departments_id);
        $oldStatus = $department->is_open;
        $department->is_open = ! $oldStatus;
        $department->save();

        ActivityLog::record(
            action: 'TOGGLE_DEPARTMENT_STATUS',
            modelType: 'Department',
            modelId: $department->id,
            description: "Operator mengubah status operasional instansi '{$department->name}' dari ".($oldStatus ? "'Buka'" : "'Tutup'").' menjadi '.($department->is_open ? "'Buka'" : "'Tutup'").'.',
            actorUserId: $user->id
        );

        return $department->is_open;
    }

    /**
     * Memanggil antrean berikutnya secara otomatis (Next Queue) dengan DB Pessimistic Lock.
     */
    public function callNext(User $user, ?string $visitNotes = null): ?Queue
    {
        return DB::transaction(function () use ($user, $visitNotes) {
            $nextQueue = Queue::where('department_id', $user->departments_id)
                ->whereDate('booking_date', Carbon::today())
                ->where('status', QueueStatus::CheckedIn->value)
                ->orderBy('is_priority', 'desc')
                ->orderBy('is_waterfall_forwarded', 'desc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if (! $nextQueue) {
                return null;
            }

            return $this->callQueueDirect($nextQueue, $user, $visitNotes);
        });
    }

    /**
     * Memanggil antrean tertentu berdasarkan ID.
     */
    public function callQueue(Queue $queue, User $user, ?string $visitNotes = null): Queue
    {
        return $this->callQueueDirect($queue, $user, $visitNotes);
    }

    /**
     * Menyelesaikan pelayanan antrean dan meneruskan ke gerai berikutnya jika ada (Waterfall Queue).
     *
     * @return Queue|null Antrean gerai lanjutan yang terbit otomatis, atau null jika kunjungan selesai.
     */
    public function finishService(Queue $queue, User $user, ?string $visitNotes = null): ?Queue
    {
        $nextQueueCreated = null;

        DB::transaction(function () use ($queue, $user, $visitNotes, &$nextQueueCreated) {
            $queue->update([
                'status' => QueueStatus::Completed->value,
                'completed_at' => now(),
                'visit_notes' => $visitNotes,
            ]);

            ActivityLog::record(
                action: 'COMPLETE_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator menyelesaikan pelayanan untuk antrean {$queue->queue_number}.",
                actorUserId: $user->id
            );

            // Alur Waterfall Queue: Cek apakah ada gerai berikutnya
            $nextDeptIds = $queue->next_department_ids;
            if (is_array($nextDeptIds) && count($nextDeptIds) > 0) {
                $targetDeptId = (int) array_shift($nextDeptIds);
                $targetDepartment = Department::find($targetDeptId);

                if ($targetDepartment) {
                    $today = Carbon::today();
                    $isPriority = (bool) $queue->is_priority;
                    $prefix = $isPriority ? 'P' : ($targetDepartment->inisial ?: 'Q');

                    // Ambil nomor urut berikutnya di gerai tujuan
                    $queueNumbers = Queue::where('department_id', $targetDepartment->id)
                        ->whereDate('booking_date', $today)
                        ->where('is_priority', $isPriority)
                        ->whereNotNull('queue_number')
                        ->lockForUpdate()
                        ->pluck('queue_number')
                        ->map(function ($num) {
                            $parts = explode('-', $num);

                            return (int) end($parts);
                        });

                    $nextNumber = $queueNumbers->isEmpty() ? 1 : $queueNumbers->max() + 1;
                    $newQueueNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
                    $rootId = $queue->parent_queue_id ?? $queue->id;
                    $nextSeq = ($queue->sequence_order ?? 1) + 1;

                    $rootBookingCode = Str::before($queue->booking_code, '-S');
                    $stepBookingCode = $rootBookingCode.'-S'.$nextSeq;

                    $nextQueueCreated = Queue::create([
                        'user_id' => $queue->user_id,
                        'parent_queue_id' => $rootId,
                        'department_id' => $targetDepartment->id,
                        'next_department_ids' => count($nextDeptIds) > 0 ? array_values($nextDeptIds) : null,
                        'booking_code' => $stepBookingCode,
                        'purpose' => $queue->purpose,
                        'session_name' => $queue->session_name,
                        'booking_date' => $today->toDateString(),
                        'queue_number' => $newQueueNumber,
                        'sequence_order' => $nextSeq,
                        'status' => QueueStatus::CheckedIn->value,
                        'is_priority' => $isPriority,
                        'is_waterfall_forwarded' => true,
                        'checked_in_at' => now(),
                    ]);

                    if (class_exists(QueueCreated::class)) {
                        event(new QueueCreated($nextQueueCreated));
                    }

                    if ($queue->user_id) {
                        Notification::create([
                            'user_id' => $queue->user_id,
                            'title' => 'Antrean Diteruskan (Multi-Gerai)',
                            'message' => "Pelayanan Anda di {$queue->department?->name} telah selesai. Antrean Anda otomatis diteruskan ke instansi {$targetDepartment->name} dengan nomor antrean {$newQueueNumber} (Prioritas Lanjutan).",
                        ]);
                    }

                    ActivityLog::record(
                        action: 'WATERFALL_FORWARD_QUEUE',
                        modelType: 'Queue',
                        modelId: $nextQueueCreated->id,
                        description: "Sistem meneruskan antrean multi-gerai secara otomatis dari '{$queue->department?->name}' ke '{$targetDepartment->name}' ({$newQueueNumber}).",
                        actorUserId: $user->id
                    );
                }
            } else {
                // Notifikasi penyelesaian akhir jika tidak ada gerai lanjutan
                if ($queue->user_id) {
                    Notification::create([
                        'user_id' => $queue->user_id,
                        'title' => 'Pelayanan Selesai',
                        'message' => "Pelayanan untuk nomor antrean {$queue->queue_number} telah selesai. Silakan isi ulasan dan berikan feedback Anda di menu Dashboard.",
                    ]);
                }
            }
        });

        if (class_exists(QueueFinished::class)) {
            event(new QueueFinished($queue));
        }

        return $nextQueueCreated;
    }

    /**
     * Melewatkan antrean aktif (Skip).
     */
    public function skipQueue(Queue $queue, User $user): void
    {
        DB::transaction(function () use ($queue, $user) {
            $queue->update([
                'status' => QueueStatus::Skipped->value,
                'completed_at' => now(),
            ]);

            ActivityLog::record(
                action: 'SKIP_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator melewatkan nomor antrean {$queue->queue_number}.",
                actorUserId: $user->id
            );
        });

        if (class_exists(QueueFinished::class)) {
            event(new QueueFinished($queue));
        }
    }

    /**
     * Forward (re-assign) a queue to a different department.
     * The queue status is reset to Checked-In so the target booth can call it next.
     *
     * @throws \RuntimeException if the queue is already Completed, Cancelled, or Skipped.
     */
    public function forwardQueue(Queue $queue, Department $targetDepartment): void
    {
        $terminalStatuses = QueueStatus::finished();
        if (in_array(QueueStatus::tryFrom($queue->status->value ?? $queue->status), $terminalStatuses, true)) {
            throw new \RuntimeException('Antrean yang sudah selesai tidak dapat diopersikan ke instansi lain.');
        }

        DB::transaction(function () use ($queue, $targetDepartment) {
            $originalDeptName = $queue->department?->name ?? 'Tidak diketahui';
            $oldQueueNumber = $queue->queue_number;

            // Generate new queue number for the target department
            $today = Carbon::today()->toDateString();
            $isPriority = (bool) $queue->is_priority;
            $queueNumbers = Queue::where('department_id', $targetDepartment->id)
                ->whereDate('booking_date', $today)
                ->where('is_priority', $isPriority)
                ->whereNotNull('queue_number')
                ->lockForUpdate()
                ->pluck('queue_number')
                ->map(function ($num) {
                    $parts = explode('-', $num);

                    return (int) end($parts);
                });

            $nextNumber = $queueNumbers->isEmpty() ? 1 : $queueNumbers->max() + 1;
            $prefix = $isPriority ? 'P' : ($targetDepartment->inisial ?: 'Q');
            $newQueueNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

            // Update queue record directly
            $queue->update([
                'department_id' => $targetDepartment->id,
                'queue_number' => $newQueueNumber,
                'status' => QueueStatus::CheckedIn->value,
                'called_at' => null,
                'completed_at' => null,
                'visit_notes' => null,
                'cancel_reason' => null,
            ]);

            ActivityLog::record(
                action: 'FORWARD_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator mengoper antrean dari instansi '{$originalDeptName}' ({$oldQueueNumber}) ke instansi '{$targetDepartment->name}' ({$newQueueNumber}).",
                actorUserId: Auth::id()
            );

            // Notify FO users
            $foUsers = User::byRole(UserRole::AdminFo)->get();
            foreach ($foUsers as $fo) {
                $fo->notifications()->create([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\QueueForwarded',
                    'data' => [
                        'title' => 'Antrean Dioper',
                        'message' => "Antrean {$oldQueueNumber} dari {$originalDeptName} telah dioper ke {$targetDepartment->name} dengan nomor baru {$newQueueNumber}.",
                    ],
                ]);
            }

            // Notify the citizen
            if ($queue->user_id) {
                Notification::create([
                    'user_id' => $queue->user_id,
                    'title' => 'Antrean Dioper',
                    'message' => "Antrean Anda telah dioper ke instansi {$targetDepartment->name} dengan nomor antrean baru {$newQueueNumber}. Silakan menunggu panggilan di loket baru.",
                ]);
            }
        });

        if (class_exists(QueueFinished::class)) {
            event(new QueueFinished($queue));
        }
    }

    /**
     * Memproses logika pemanggilan antrean (Internal Helper).
     */
    private function callQueueDirect(Queue $queue, User $user, ?string $visitNotes = null): Queue
    {
        $today = Carbon::today();

        $updatedQueue = DB::transaction(function () use ($queue, $today, $user, $visitNotes) {
            // Selesaikan antrean Serving lain di instansi (cegah stuck) dengan menyimpan catatan kunjungan
            Queue::where('department_id', $queue->department_id)
                ->whereDate('booking_date', $today)
                ->where('status', QueueStatus::Serving->value)
                ->where('id', '!=', $queue->id)
                ->update([
                    'status' => QueueStatus::Completed->value,
                    'completed_at' => now(),
                    'visit_notes' => $visitNotes,
                ]);

            $queue->update([
                'status' => QueueStatus::Serving->value,
                'called_at' => now(),
            ]);

            // Notify the citizen that they are called/recalled
            if ($queue->user_id) {
                Notification::create([
                    'user_id' => $queue->user_id,
                    'title' => 'Nomor Antrean Dipanggil',
                    'message' => "Nomor antrean {$queue->queue_number} Anda dipanggil di loket {$queue->department?->nomor_loket}. Silakan menuju loket sekarang.",
                ]);
            }

            ActivityLog::record(
                action: 'CALL_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator memanggil nomor antrean {$queue->queue_number} ke loket.",
                actorUserId: $user->id
            );

            return $queue;
        });

        $loadedQueue = $updatedQueue->load(['department']);

        if (class_exists(QueueCalled::class)) {
            event(new QueueCalled($loadedQueue));
        }

        return $loadedQueue;
    }
}
