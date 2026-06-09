<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\QueueCalled;
use App\Events\QueueFinished;
use App\Mail\FeedbackRequestMail;
use App\Models\ActivityLog;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CounterController extends Controller
{
    /**
     * Tampilkan halaman utama operator loket gerai.
     * GET /antrean
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        // Memeriksa penugasan loket
        if (! $user->counter_id) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        $counter = Counter::with('department')->find($user->counter_id);
        if (! $counter) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        $today = Carbon::today();

        // 1. Antrean yang sedang aktif dilayani (Serving)
        $activeQueue = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Serving')
            ->with(['booking.user', 'visitor', 'service'])
            ->first();

        // 2. Daftar antrean menunggu (Waiting)
        $waitingQueues = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Waiting')
            ->with(['booking.user', 'visitor', 'service'])
            ->orderBy('id', 'asc')
            ->get();

        // 3. Daftar antrean terlewat (Skipped)
        $skippedQueues = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Skipped')
            ->with(['booking.user', 'visitor', 'service'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // 4. Hitung Metrik Pelayanan Hari Ini
        $remainingCount = $waitingQueues->count();

        // Rata-rata durasi pelayanan (dalam menit)
        $completedToday = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Completed')
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedToday->isEmpty()) {
            $avgServiceTime = 12; // Nilai default estimasi (12 menit)
        } else {
            $totalSeconds = $completedToday->sum(fn (Queue $q) => $q->calculateDuration());
            $avgServiceTime = (int) round(($totalSeconds / $completedToday->count()) / 60);
            if ($avgServiceTime < 1) {
                $avgServiceTime = 1;
            }
        }

        return view('dashboard.dashboard', compact(
            'counter',
            'activeQueue',
            'waitingQueues',
            'skippedQueues',
            'remainingCount',
            'avgServiceTime'
        ));
    }

    /**
     * Update status operasional loket.
     * POST /api/counter/status
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:aktif,nonaktif,istirahat'],
        ]);

        $user = Auth::user();
        if (! $user->counter_id) {
            return response()->json(['message' => 'Anda belum ditugaskan ke loket mana pun.'], 403);
        }

        $counter = Counter::findOrFail($user->counter_id);
        $oldStatus = $counter->status;
        $counter->update(['status' => $request->input('status')]);

        // Catat di ActivityLog
        ActivityLog::record(
            action: 'UPDATE_COUNTER_STATUS',
            modelType: 'Counter',
            modelId: $counter->id,
            description: "Operator mengubah status loket '{$counter->name}' dari '{$oldStatus}' menjadi '{$counter->status}'.",
            actorUserId: $user->id
        );

        return response()->json([
            'success' => true,
            'status' => $counter->status,
        ]);
    }

    /**
     * Memanggil antrean berikutnya secara otomatis (Next Queue).
     * POST /api/queues/call-next
     */
    public function callNext(): JsonResponse
    {
        $user = Auth::user();
        if (! $user->counter_id) {
            return response()->json(['message' => 'Anda belum ditugaskan ke loket mana pun.'], 403);
        }

        $today = Carbon::today();

        // Cari antrean waiting berikutnya
        $nextQueue = Queue::where('counter_id', $user->counter_id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Waiting')
            ->orderBy('id', 'asc')
            ->first();

        if (! $nextQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada antrean berikutnya yang sedang menunggu.',
            ]);
        }

        return $this->callQueueDirect($nextQueue);
    }

    /**
     * Memanggil antrean tertentu berdasarkan ID (termasuk panggil ulang/panggil balik skipped).
     * POST /api/queues/{queue}/call
     */
    public function callQueue(Queue $queue): JsonResponse
    {
        $this->authorize('manage', $queue);

        return $this->callQueueDirect($queue);
    }

    /**
     * Menyelesaikan pelayanan antrean.
     * POST /api/queues/{queue}/finish
     */
    public function finishService(Queue $queue): JsonResponse
    {
        $this->authorize('manage', $queue);

        if ($queue->status !== 'Serving') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya antrean berstatus Serving yang dapat diselesaikan.',
            ], 422);
        }

        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);

            // Kirim notifikasi sistem ke pengguna (jika booking online)
            if ($queue->booking && $queue->booking->user_id) {
                Notification::create([
                    'user_id' => $queue->booking->user_id,
                    'title' => 'Pelayanan Selesai',
                    'message' => "Pelayanan untuk nomor antrean {$queue->queue_number} telah selesai. Silakan isi ulasan dan berikan feedback Anda di menu Dashboard.",
                ]);
            }

            // Catat log
            ActivityLog::record(
                action: 'COMPLETE_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator menyelesaikan pelayanan untuk antrean {$queue->queue_number}.",
                actorUserId: Auth::id()
            );
        });

        // Trigger Event
        event(new QueueFinished($queue));

        // Kirim email notifikasi secara asinkron/aman (BR-11)
        if ($queue->booking && $queue->booking->user && $queue->booking->user->email) {
            try {
                Mail::to($queue->booking->user->email)->send(new FeedbackRequestMail($queue));
            } catch (\Throwable $e) {
                Log::error("SMTP Error: Gagal mengirim email notifikasi ulasan ke {$queue->booking->user->email}. Detil: ".$e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status antrean berhasil diperbarui.',
        ]);
    }

    /**
     * Melewatkan antrean aktif (Skip).
     * POST /api/queues/{queue}/skip
     */
    public function skipQueue(Queue $queue): JsonResponse
    {
        $this->authorize('manage', $queue);

        if ($queue->status !== 'Serving') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya antrean aktif yang sedang dilayani yang dapat dilewati.',
            ], 422);
        }

        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => 'Skipped',
                'completed_at' => now(), // Catat waktu dilewati
            ]);

            // Catat log
            ActivityLog::record(
                action: 'SKIP_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator melewatkan nomor antrean {$queue->queue_number}.",
                actorUserId: Auth::id()
            );
        });

        // Trigger Event
        event(new QueueFinished($queue));

        return response()->json([
            'success' => true,
            'message' => 'Antrean berhasil dilewati.',
        ]);
    }

    /**
     * Memproses logika pemanggilan antrean (Internal Helper).
     */
    protected function callQueueDirect(Queue $queue): JsonResponse
    {
        $today = Carbon::today();

        $updatedQueue = DB::transaction(function () use ($queue, $today) {
            // 1. Selesaikan antrean lain di loket yang masih Serving (jika ada) demi mencegah stuck
            Queue::where('counter_id', $queue->counter_id)
                ->whereDate('queue_date', $today)
                ->where('status', 'Serving')
                ->where('id', '!=', $queue->id)
                ->update([
                    'status' => 'Completed',
                    'completed_at' => now(),
                ]);

            // 2. Update status antrean target ke Serving
            $queue->update([
                'status' => 'Serving',
                'called_at' => now(),
            ]);

            // 3. Catat di ActivityLog
            ActivityLog::record(
                action: 'CALL_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator memanggil nomor antrean {$queue->queue_number} ke loket.",
                actorUserId: Auth::id()
            );

            return $queue;
        });

        // 4. Broadcast event pemanggilan
        $loadedQueue = $updatedQueue->load(['counter.department', 'visitor', 'booking.user', 'service']);
        event(new QueueCalled($loadedQueue));

        return response()->json([
            'success' => true,
            'queue' => [
                'id' => $loadedQueue->id,
                'queue_number' => $loadedQueue->queue_number,
                'status' => $loadedQueue->status,
                'called_at' => $loadedQueue->called_at ? $loadedQueue->called_at->toIso8601String() : null,
                'visitor' => [
                    'name' => $loadedQueue->visitor ? $loadedQueue->visitor->name : ($loadedQueue->booking && $loadedQueue->booking->user ? $loadedQueue->booking->user->name : 'Warga'),
                    'nik' => $loadedQueue->visitor ? $loadedQueue->visitor->nik : ($loadedQueue->booking && $loadedQueue->booking->user ? $loadedQueue->booking->user->nik : '-'),
                ],
                'service' => [
                    'name' => $loadedQueue->service ? $loadedQueue->service->name : 'Layanan Umum',
                ],
            ],
        ]);
    }

    /**
     * Toggle status operasional instansi (buka/tutup) untuk instansi petugas yang bersangkutan.
     * POST /api/department/toggle-status
     */
    public function toggleDepartmentStatus(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            return response()->json(['message' => 'Anda tidak ditugaskan ke instansi mana pun.'], 403);
        }

        $department = Department::findOrFail($user->departments_id);
        $oldStatus = $department->is_open;
        $department->is_open = ! $oldStatus;
        $department->save();

        // Catat di ActivityLog
        ActivityLog::record(
            action: 'TOGGLE_DEPARTMENT_STATUS',
            modelType: 'Department',
            modelId: $department->id,
            description: "Operator mengubah status operasional instansi '{$department->name}' dari ".($oldStatus ? "'Buka'" : "'Tutup'").' menjadi '.($department->is_open ? "'Buka'" : "'Tutup'").'.',
            actorUserId: $user->id
        );

        return response()->json([
            'success' => true,
            'is_open' => $department->is_open,
            'message' => 'Status operasional instansi berhasil diperbarui.',
        ]);
    }
}
