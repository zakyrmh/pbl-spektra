<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Data\PapanPanggilDashboardData;
use App\Http\Controllers\Controller;
use App\Http\Requests\PapanPanggilActionRequest;
use App\Models\Department;
use App\Models\Queue;
use App\Services\PapanPanggilService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PapanPanggilController extends Controller
{
    public function __construct(
        protected PapanPanggilService $papanPanggilService
    ) {}

    /**
     * Tampilkan papan panggil instansi admin gerai.
     * GET /admin/papan-panggil
     */
    public function index(): View
    {
        $user = Auth::user();

        if (! $user->departments_id) {
            abort(403, 'Anda tidak ditugaskan ke instansi mana pun.');
        }

        $department = Department::findOrFail($user->departments_id);

        // Pass department mapped as counter to preserve frontend view layout
        $counter = (object) [
            'name' => 'Loket '.($department->nomor_loket ?: $department->inisial),
        ];

        $today = Carbon::today();

        $activeBookingModel = $this->papanPanggilService->getCurrentQueue($department, $today);
        $activeBooking = $activeBookingModel ? PapanPanggilDashboardData::fromModel($activeBookingModel) : null;

        $sisaBookingModels = $this->papanPanggilService->getRemainingQueues($department, $today);
        $sisaBookings = $sisaBookingModels->map(fn ($q) => PapanPanggilDashboardData::fromModel($q));

        return view('admin.papan-panggil', compact(
            'department',
            'counter',
            'activeBooking',
            'sisaBookings'
        ));
    }

    /**
     * Panggil antrean berikutnya.
     * POST /admin/papan-panggil/next
     */
    public function next(PapanPanggilActionRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->departments_id) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $department = Department::findOrFail($user->departments_id);
        $today = Carbon::today();

        $nextQueue = $this->papanPanggilService->callNext($department, $today);

        if (! $nextQueue) {
            return back()->with('error', 'Tidak ada antrean tersisa untuk hari ini.');
        }

        return back()->with('success', 'Antrean '.$nextQueue->booking_code.' berhasil dipanggil.');
    }

    /**
     * Tandai antrean aktif sebagai selesai (Completed).
     * POST /admin/papan-panggil/{booking}/complete
     */
    public function complete(PapanPanggilActionRequest $request, Queue $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($booking->department_id !== $user->departments_id) {
            abort(403, 'Anda tidak berhak mengelola antrean instansi lain.');
        }

        $this->papanPanggilService->complete($booking);

        return back()->with('success', 'Antrean '.$booking->booking_code.' selesai dilayani.');
    }

    /**
     * Lewati/batalkan antrean aktif (Cancelled).
     * POST /admin/papan-panggil/{booking}/skip
     */
    public function skip(PapanPanggilActionRequest $request, Queue $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($booking->department_id !== $user->departments_id) {
            abort(403, 'Anda tidak berhak mengelola antrean instansi lain.');
        }

        $reason = $request->input('cancel_reason');
        $this->papanPanggilService->skip($booking, $reason);

        return back()->with('success', 'Antrean '.$booking->booking_code.' berhasil dilewati.');
    }
}
