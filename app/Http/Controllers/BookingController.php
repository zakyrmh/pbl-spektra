<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Tampilkan riwayat booking milik pengunjung yang login.
     */
    public function index(): View
    {
        $bookings = Auth::user()->bookings()
            ->with(['service.department', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('booking.index', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Tampilkan form pembuatan booking baru.
     */
    public function create(): View
    {
        $departments = Department::with('services')->get();

        // Get schedules grouped by service to easily filter on client side
        $schedules = Schedule::where('date', '>=', now()->toDateString())
            ->where('is_open', true)
            ->whereColumn('quota_used', '<', 'quota_total')
            ->orderBy('date', 'asc')
            ->get();

        return view('booking.create', [
            'departments' => $departments,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Simpan reservasi booking antrean mandiri.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'schedule_id' => ['required', 'exists:schedules,id'],
        ], [
            'service_id.required' => 'Silakan pilih jenis pelayanan.',
            'schedule_id.required' => 'Silakan pilih jadwal pelayanan.',
        ]);

        try {
            $booking = $this->bookingService->createBooking(
                Auth::user(),
                (int) $request->input('service_id'),
                (int) $request->input('schedule_id')
            );

            return redirect()
                ->route('booking.show', $booking)
                ->with('success', 'Reservasi antrean mandiri berhasil dibuat.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Tampilkan halaman tiket digital booking.
     */
    public function show(Booking $booking): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Security check: Only the owner, FO admin, or Super Admin can view the ticket
        if ($booking->user_id !== $user->id && ! in_array($user->role->value, ['super_admin', 'admin_fo'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat tiket ini.');
        }

        // Calculate estimated queue position (number of pending/checked-in bookings before this one on the same date)
        $estimatedPosition = Booking::where('service_id', $booking->service_id)
            ->where('booking_date', $booking->booking_date)
            ->where('id', '<', $booking->id)
            ->whereIn('status', ['Pending', 'Checked-In'])
            ->count() + 1;

        return view('booking.show', [
            'booking' => $booking->load(['service.department', 'schedule']),
            'estimatedPosition' => $estimatedPosition,
        ]);
    }
}
