<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminGerai\ForwardQueueRequest;
use App\Models\Department;
use App\Models\Queue;
use App\Services\AdminGerai\BoothOperationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class CounterController extends Controller
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
        $data = $this->boothService->getDashboardData($user);

        if (! $data) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        return view('dashboard.dashboard', [
            'department' => $data->department,
            'activeQueue' => $data->activeQueue,
            'waitingQueues' => $data->waitingQueues,
            'skippedQueues' => $data->skippedQueues,
            'remainingCount' => $data->remainingCount,
            'avgServiceTime' => $data->avgServiceTime,
            'activeDepartments' => $data->activeDepartments,
        ]);
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

        $this->boothService->updateStatus($user, $request->input('status'));

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

        $isOpen = $this->boothService->toggleDepartmentStatus($user);

        return response()->json([
            'success' => true,
            'is_open' => $isOpen,
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

        $nextQueue = $this->boothService->callNext($user);

        if (! $nextQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada antrean berikutnya yang sedang menunggu.',
            ]);
        }

        return response()->json([
            'success' => true,
            'queue' => [
                'id' => $nextQueue->id,
                'queue_number' => $nextQueue->queue_number,
                'status' => $nextQueue->status->value ?? $nextQueue->status,
                'called_at' => $nextQueue->called_at?->toIso8601String(),
                'user' => [
                    'name' => $nextQueue->user?->name ?? 'Warga',
                    'nik' => $nextQueue->user?->nik ?? '-',
                ],
                'purpose' => $nextQueue->purpose ?? 'Layanan Umum',
            ],
        ]);
    }

    /**
     * Memanggil antrean tertentu berdasarkan ID.
     * POST /api/queues/{queue}/call
     */
    public function callQueue(Queue $queue): JsonResponse
    {
        $user = Auth::user();
        if ($queue->department_id !== $user->departments_id) {
            abort(403);
        }

        $calledQueue = $this->boothService->callQueue($queue, $user);

        return response()->json([
            'success' => true,
            'queue' => [
                'id' => $calledQueue->id,
                'queue_number' => $calledQueue->queue_number,
                'status' => $calledQueue->status->value ?? $calledQueue->status,
                'called_at' => $calledQueue->called_at?->toIso8601String(),
                'user' => [
                    'name' => $calledQueue->user?->name ?? 'Warga',
                    'nik' => $calledQueue->user?->nik ?? '-',
                ],
                'purpose' => $calledQueue->purpose ?? 'Layanan Umum',
            ],
        ]);
    }

    /**
     * Menyelesaikan pelayanan antrean.
     * POST /api/queues/{queue}/finish
     */
    public function finishService(Queue $queue): JsonResponse
    {
        $user = Auth::user();
        if ($queue->department_id !== $user->departments_id) {
            abort(403);
        }

        if (($queue->status->value ?? $queue->status) !== 'Serving') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya antrean berstatus Serving yang dapat diselesaikan.',
            ], 422);
        }

        $this->boothService->finishService($queue, $user);

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
        $user = Auth::user();
        if ($queue->department_id !== $user->departments_id) {
            abort(403);
        }

        if (($queue->status->value ?? $queue->status) !== 'Serving') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya antrean aktif yang sedang dilayani yang dapat dilewati.',
            ], 422);
        }

        $this->boothService->skipQueue($queue, $user);

        return response()->json([
            'success' => true,
            'message' => 'Antrean berhasil dilewati.',
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
}
