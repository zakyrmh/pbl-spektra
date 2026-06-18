<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFO\CancelBookingRequest;
use App\Models\Queue;
use App\Models\User;
use App\Services\AdminFO\BookingCancellationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class BookingCancellationController extends Controller
{
    public function __construct(
        protected BookingCancellationService $cancellationService
    ) {}

    /**
     * Tampilkan semua daftar booking online yang berstatus 'Booked' (Pending).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $bookings = $this->cancellationService->getPendingBookings($search);

        return view('admin.fo.bookings.index', [
            'bookings' => $bookings,
            'search' => $search,
        ]);
    }

    /**
     * Proses pembatalan booking oleh Admin FO dengan memberikan alasan.
     */
    public function cancel(CancelBookingRequest $request, Queue $booking): RedirectResponse
    {
        /** @var User $actor */
        $actor = Auth::user();
        $userRole = $actor->role instanceof \BackedEnum ? $actor->role->value : $actor->role;

        if (! in_array($userRole, ['admin_fo', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk membatalkan booking ini.');
        }

        $reason = trim($request->input('reason'));

        try {
            $booking->cancelled_at = now();
            $this->cancellationService->cancelBooking($booking, $reason, $actor);

            return back()
                ->with('success', "Booking <strong>{$booking->booking_code}</strong> atas nama <strong>{$booking->user->name}</strong> berhasil dibatalkan.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal memproses pembatalan: '.$e->getMessage()]);
        }
    }
}
