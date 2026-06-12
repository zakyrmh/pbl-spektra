<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Events\QueueCalled;
use App\Events\QueueFinished;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForwardQueueRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Queue;
use App\Services\BoothOperationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CounterController extends Controller
{
    public function __construct(
        protected BoothOperationService $boothService
    ) {}

    /**
     * Tampilkan halaman utama operator loket gerai.
     * GET /antrean
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        if (! $user->departments_id) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        $department = Department::find($user->departments_id);
        if (! $department) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        $today = Carbon::today();

        // Antrean yang sedang aktif dilayani (Serving)
        $activeQueue = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', 'Serving')
            ->with(['user'])
            ->first();

        // Daftar antrean menunggu (Checked-In)
        $waitingQueues = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', 'Checked-In')
            ->with(['user'])
            ->orderBy('id', 'asc')
            ->get();

        // Daftar antrean terlewat (Skipped)
        $skippedQueues = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', 'Skipped')
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Hitung Metrik
        $remainingCount = $waitingQueues->count();

        // Rata-rata durasi pelayanan (dalam menit)
        $completedToday = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', 'Completed')
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

        return view('dashboard.dashboard', compact(
            'department',
            'activeQueue',
            'waitingQueues',
            'skippedQueues',
            'remainingCount',
            'avgServiceTime',
            'activeDepartments',
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
        if (! $user->departments_id) {
            return response()->json(['message' => 'Anda belum ditugaskan ke instansi mana pun.'], 403);
        }

        $department = Department::findOrFail($user->departments_id);

        Cache::put("loket_status_{$department->id}", $request->input('status'), now()->addDay());

        ActivityLog::record(
            action: 'UPDATE_COUNTER_STATUS',
            modelType: 'Department',
            modelId: $department->id,
            description: "Operator mengubah status loket instansi '{$department->name}' menjadi '{$request->input('status')}'.",
            actorUserId: $user->id
        );

        return response()->json([
            'success' => true,
            'status' => $request->input('status'),
        ]);
    }

    /**
     * Toggle status operasional instansi (buka/tutup).
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

    /**
     * Memanggil antrean berikutnya secara otomatis (Next Queue).
     * POST /api/queues/call-next
     */
    public function callNext(): JsonResponse
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            return response()->json(['message' => 'Anda belum ditugaskan ke instansi mana pun.'], 403);
        }

        $nextQueue = Queue::where('department_id', $user->departments_id)
            ->whereDate('booking_date', Carbon::today())
            ->where('status', 'Checked-In')
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
     * Memanggil antrean tertentu berdasarkan ID.
     * POST /api/queues/{queue}/call
     */
    public function callQueue(Queue $queue): JsonResponse
    {
        if ($queue->department_id !== Auth::user()->departments_id) {
            abort(403);
        }

        return $this->callQueueDirect($queue);
    }

    /**
     * Menyelesaikan pelayanan antrean.
     * POST /api/queues/{queue}/finish
     */
    public function finishService(Queue $queue): JsonResponse
    {
        if ($queue->department_id !== Auth::user()->departments_id) {
            abort(403);
        }

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

            if ($queue->user) {
                $queue->user->notifications()->create([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\QueueFinished',
                    'data' => [
                        'title' => 'Pelayanan Selesai',
                        'message' => "Pelayanan untuk nomor antrean {$queue->queue_number} telah selesai. Silakan isi ulasan dan berikan feedback Anda di menu Dashboard.",
                    ],
                ]);
            }

            ActivityLog::record(
                action: 'COMPLETE_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator menyelesaikan pelayanan untuk antrean {$queue->queue_number}.",
                actorUserId: Auth::id()
            );
        });

        if (class_exists(QueueFinished::class)) {
            event(new QueueFinished($queue));
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
        if ($queue->department_id !== Auth::user()->departments_id) {
            abort(403);
        }

        if ($queue->status !== 'Serving') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya antrean aktif yang sedang dilayani yang dapat dilewati.',
            ], 422);
        }

        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => 'Skipped',
                'completed_at' => now(),
            ]);

            ActivityLog::record(
                action: 'SKIP_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator melewatkan nomor antrean {$queue->queue_number}.",
                actorUserId: Auth::id()
            );
        });

        if (class_exists(QueueFinished::class)) {
            event(new QueueFinished($queue));
        }

        return response()->json([
            'success' => true,
            'message' => 'Antrean berhasil dilewati.',
        ]);
    }

    /**
     * Menunda antrean aktif (Hold).
     * POST /api/queues/{queue}/hold
     */
    public function holdQueue(Queue $queue): JsonResponse
    {
        if ($queue->department_id !== Auth::user()->departments_id) {
            abort(403);
        }

        try {
            $this->boothService->holdQueue($queue);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Antrean {$queue->queue_number} berhasil ditunda (Hold).",
        ]);
    }

    /**
     * Mengoper antrean ke instansi lain (Forward).
     * POST /api/queues/{queue}/forward
     */
    public function forwardQueue(ForwardQueueRequest $request, Queue $queue): JsonResponse
    {
        if ($queue->department_id !== Auth::user()->departments_id) {
            abort(403);
        }

        $targetDepartment = Department::findOrFail($request->integer('target_department_id'));

        try {
            $this->boothService->forwardQueue($queue, $targetDepartment);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Antrean {$queue->queue_number} berhasil diopersikan ke instansi {$targetDepartment->name}.",
        ]);
    }

    /**
     * Memproses logika pemanggilan antrean (Internal Helper).
     */
    private function callQueueDirect(Queue $queue): JsonResponse
    {
        $today = Carbon::today();

        $updatedQueue = DB::transaction(function () use ($queue, $today) {
            // Selesaikan antrean Serving lain di instansi (cegah stuck)
            Queue::where('department_id', $queue->department_id)
                ->whereDate('booking_date', $today)
                ->where('status', 'Serving')
                ->where('id', '!=', $queue->id)
                ->update([
                    'status' => 'Completed',
                    'completed_at' => now(),
                ]);

            $queue->update([
                'status' => 'Serving',
                'called_at' => now(),
            ]);

            ActivityLog::record(
                action: 'CALL_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator memanggil nomor antrean {$queue->queue_number} ke loket.",
                actorUserId: Auth::id()
            );

            return $queue;
        });

        $loadedQueue = $updatedQueue->load(['department']);

        if (class_exists(QueueCalled::class)) {
            event(new QueueCalled($loadedQueue));
        }

        return response()->json([
            'success' => true,
            'queue' => [
                'id' => $loadedQueue->id,
                'queue_number' => $loadedQueue->queue_number,
                'status' => $loadedQueue->status,
                'called_at' => $loadedQueue->called_at?->toIso8601String(),
                'user' => [
                    'name' => $loadedQueue->user?->name ?? 'Warga',
                    'nik' => $loadedQueue->user?->nik ?? '-',
                ],
                'purpose' => $loadedQueue->purpose ?? 'Layanan Umum',
            ],
        ]);
    }
}
