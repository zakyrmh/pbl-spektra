<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Data\WaitingListDashboardData;
use App\Enums\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\WaitingListActionRequest;
use App\Models\Department;
use App\Models\Queue;
use App\Services\WaitingListService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DaftarTungguController extends Controller
{
    public function __construct(
        protected WaitingListService $waitingListService
    ) {}

    /**
     * Tampilkan daftar tunggu instansi admin gerai.
     * GET /admin/daftar-tunggu
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if (! $user->departments_id) {
            abort(403, 'Anda tidak ditugaskan ke instansi mana pun.');
        }

        $department = Department::findOrFail($user->departments_id);

        // Schedules are deleted, pass empty collection to keep layout fallback functional
        $schedules = collect();

        $today = Carbon::today();
        $search = $request->filled('search') ? trim($request->input('search')) : null;

        // Fetch queues for the three categories
        $pendingModels = $this->waitingListService->getQueues($department, $today, [QueueStatus::Booked->value], $search);
        $pendingBookings = $pendingModels->map(fn ($q) => WaitingListDashboardData::fromModel($q));

        $checkedInModels = $this->waitingListService->getQueues($department, $today, [QueueStatus::CheckedIn->value], $search);
        $checkedInBookings = $checkedInModels->map(fn ($q) => WaitingListDashboardData::fromModel($q));

        $cancelledModels = $this->waitingListService->getQueues($department, $today, [QueueStatus::Cancelled->value, QueueStatus::Skipped->value], $search);
        $cancelledBookings = $cancelledModels->map(fn ($q) => WaitingListDashboardData::fromModel($q));

        return view('admin.daftar-tunggu', compact(
            'department',
            'schedules',
            'pendingBookings',
            'checkedInBookings',
            'cancelledBookings'
        ));
    }

    /**
     * Check-in manual booking dari daftar tunggu.
     * POST /admin/daftar-tunggu/{booking}/check-in
     */
    public function checkIn(WaitingListActionRequest $request, Queue $booking): RedirectResponse
    {
        try {
            $this->waitingListService->checkIn($booking);

            return back()->with('success', "Check-in manual berhasil! Warga {$booking->user->name} dengan antrean {$booking->queue_number} telah masuk daftar tunggu.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses check-in: '.$e->getMessage());
        }
    }

    /**
     * Pulihkan booking yang dibatalkan.
     * POST /admin/daftar-tunggu/{booking}/restore
     */
    public function restore(WaitingListActionRequest $request, Queue $booking): RedirectResponse
    {
        try {
            $this->waitingListService->restore($booking);

            return back()->with('success', "Status booking {$booking->booking_code} berhasil dipulihkan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulihkan booking: '.$e->getMessage());
        }
    }
}
