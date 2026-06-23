<?php

declare(strict_types=1);

namespace App\Services\AdminGerai;

use App\Data\AdminGerai\BoothDashboardData;
use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Events\QueueCalled;
use App\Events\QueueFinished;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
     * Memanggil antrean berikutnya secara otomatis (Next Queue).
     */
    public function callNext(User $user): ?Queue
    {
        $nextQueue = Queue::where('department_id', $user->departments_id)
            ->whereDate('booking_date', Carbon::today())
            ->where('status', QueueStatus::CheckedIn->value)
            ->orderBy('id', 'asc')
            ->first();

        if (! $nextQueue) {
            return null;
        }

        return $this->callQueueDirect($nextQueue, $user);
    }

    /**
     * Memanggil antrean tertentu berdasarkan ID.
     */
    public function callQueue(Queue $queue, User $user): Queue
    {
        return $this->callQueueDirect($queue, $user);
    }

    /**
     * Menyelesaikan pelayanan antrean.
     */
    public function finishService(Queue $queue, User $user): void
    {
        DB::transaction(function () use ($queue, $user) {
            $queue->update([
                'status' => QueueStatus::Completed->value,
                'completed_at' => now(),
            ]);

            // Kirim notifikasi ke warga menggunakan model Notification custom proyek ini
            if ($queue->user_id) {
                Notification::create([
                    'user_id' => $queue->user_id,
                    'title' => 'Pelayanan Selesai',
                    'message' => "Pelayanan untuk nomor antrean {$queue->queue_number} telah selesai. Silakan isi ulasan dan berikan feedback Anda di menu Dashboard.",
                ]);
            }

            ActivityLog::record(
                action: 'COMPLETE_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator menyelesaikan pelayanan untuk antrean {$queue->queue_number}.",
                actorUserId: $user->id
            );
        });

        if (class_exists(QueueFinished::class)) {
            event(new QueueFinished($queue));
        }
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

            $queue->update([
                'status' => QueueStatus::Completed->value,
                'completed_at' => now(),
                'cancel_reason' => "Dialihkan ke Gerai {$targetDepartment->name}.",
            ]);

            ActivityLog::record(
                action: 'FORWARD_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator menyelesaikan antrean {$queue->queue_number} dan dialihkan dari instansi '{$originalDeptName}' ke instansi '{$targetDepartment->name}'.",
                actorUserId: Auth::id()
            );

            // Notify FO users to re-register the walk-in
            $foUsers = User::byRole(UserRole::AdminFo)->get();
            foreach ($foUsers as $fo) {
                $fo->notifications()->create([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\QueueForwarded',
                    'data' => [
                        'title' => 'Pengunjung Oper Antrean',
                        'message' => "Pengunjung dari antrean {$queue->queue_number} dialihkan ke {$targetDepartment->name}. Mohon daftarkan ulang sebagai Walk-In.",
                    ],
                ]);
            }

            // Fallback flash session as requested in instructions just in case FO relies on session somehow
            session()->flash('transfer_notification', true);

            // Notify the citizen about the forwarding
            if ($queue->user_id) {
                Notification::create([
                    'user_id' => $queue->user_id,
                    'title' => 'Antrean Dipindahkan',
                    'message' => "Nomor antrean {$queue->queue_number} Anda telah dipindahkan ke instansi {$targetDepartment->name}. Silakan menunggu panggilan di loket baru.",
                ]);
            }
        });
    }

    /**
     * Memproses logika pemanggilan antrean (Internal Helper).
     */
    private function callQueueDirect(Queue $queue, User $user): Queue
    {
        $today = Carbon::today();

        $updatedQueue = DB::transaction(function () use ($queue, $today, $user) {
            // Selesaikan antrean Serving lain di instansi (cegah stuck)
            Queue::where('department_id', $queue->department_id)
                ->whereDate('booking_date', $today)
                ->where('status', QueueStatus::Serving->value)
                ->where('id', '!=', $queue->id)
                ->update([
                    'status' => QueueStatus::Completed->value,
                    'completed_at' => now(),
                ]);

            $queue->update([
                'status' => QueueStatus::Serving->value,
                'called_at' => now(),
            ]);

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
