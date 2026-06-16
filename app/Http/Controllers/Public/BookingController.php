<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreBookingRequest;
use App\Models\Department;
use App\Models\Queue;
use App\Services\Public\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Tampilkan riwayat booking milik pengunjung yang login.
     */
    public function index(): View
    {
        $bookings = $this->bookingService->getCustomerBookingHistory((int) Auth::id());

        return view('booking.index', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Tampilkan form pembuatan booking baru.
     */
    public function create(): View
    {
        // Ambil hanya instansi yang aktif/buka
        $departments = Department::where('is_open', true)->get();

        return view('booking.create', [
            'departments' => $departments,
            'schedules' => [],
            'sessions' => ['Sesi 1', 'Sesi 2'],
        ]);
    }

    /**
     * Simpan reservasi booking antrean mandiri.
     */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $booking = $this->bookingService->processBookingCreation(
                (int) Auth::id(),
                $request->validated()
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
    public function show(Queue $booking): View
    {
        Gate::authorize('view', $booking);

        $estimatedPosition = $this->bookingService->calculateEstimatedPosition($booking);

        return view('booking.show', [
            'booking' => $booking->load('department'),
            'estimatedPosition' => $estimatedPosition,
        ]);
    }
}
