<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Data\AdminGerai\WaitingListDashboardData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminGerai\WaitingListActionRequest;
use App\Models\Department;
use App\Models\Queue;
use App\Services\AdminGerai\WaitingListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class DaftarTungguController extends Controller
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

        $search = $request->filled('search') ? trim($request->input('search')) : null;

        // Query utama sesuai dengan instruksi teknis, menggunakan status 'Checked-In'
        // (database enum status untuk Waiting sesuai Aturan #1) dan 'Serving'.
        $queues = Queue::excludeCancelled()
            ->where('department_id', auth()->user()->department_id)
            ->whereIn('status', ['Checked-In', 'Serving'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('booking_code', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
                });
            })
            ->with('user')
            ->get();

        $waitingBookings = $queues->where('status', 'Checked-In')
            ->map(fn ($q) => WaitingListDashboardData::fromModel($q));

        $servingBookings = $queues->where('status', 'Serving')
            ->map(fn ($q) => WaitingListDashboardData::fromModel($q));

        return view('admin.daftar-tunggu', [
            'department' => $department,
            'schedules' => $schedules,
            'waitingBookings' => $waitingBookings,
            'servingBookings' => $servingBookings,
        ]);
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

    /**
     * Batalkan booking dari daftar tunggu.
     * POST /admin/daftar-tunggu/{booking}/cancel
     */
    public function cancel(WaitingListActionRequest $request, Queue $booking): RedirectResponse
    {
        if ($booking->department_id !== Auth::user()->departments_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk membatalkan booking ini.');
        }

        try {
            $this->waitingListService->cancel($booking, $request->reason);

            return back()->with('success', 'Booking berhasil dibatalkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembatalan: '.$e->getMessage());
        }
    }
}
