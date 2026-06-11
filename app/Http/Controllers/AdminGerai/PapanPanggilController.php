<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PapanPanggilController extends Controller
{
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
        $counter = Counter::where('department_id', $department->id)->first();

        // Ambil active booking dari session
        $activeBooking = null;
        $activeBookingId = session('papan_panggil_active_booking_id');

        if ($activeBookingId) {
            $activeBooking = Booking::with('user')->find($activeBookingId);
            // Bersihkan jika status booking sudah bukan Pending / Checked-In
            if ($activeBooking && ! in_array($activeBooking->status, ['Pending', 'Checked-In'])) {
                session()->forget('papan_panggil_active_booking_id');
                $activeBooking = null;
            }
        }

        // Live-feed sisa antrean hari ini
        $sisaBookings = Booking::where('booking_date', Carbon::today())
            ->whereHas('service', fn ($query) => $query->where('department_id', $department->id))
            ->whereIn('status', ['Pending', 'Checked-In'])
            ->when($activeBooking, fn ($query) => $query->where('id', '!=', $activeBooking->id))
            ->orderBy('id', 'asc')
            ->with('user')
            ->get();

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
    public function next(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->departments_id) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $nextBooking = Booking::where('booking_date', Carbon::today())
            ->whereHas('service', fn ($query) => $query->where('department_id', $user->departments_id))
            ->whereIn('status', ['Pending', 'Checked-In'])
            ->orderBy('id', 'asc')
            ->first();

        if (! $nextBooking) {
            return back()->with('error', 'Tidak ada antrean tersisa untuk hari ini.');
        }

        // Jika statusnya masih Pending, otomatis ubah menjadi Checked-In saat dipanggil
        if ($nextBooking->status === 'Pending') {
            $nextBooking->update([
                'status' => 'Checked-In',
                'checked_in_at' => now(),
            ]);
        }

        session(['papan_panggil_active_booking_id' => $nextBooking->id]);

        return back()->with('success', 'Antrean '.$nextBooking->booking_code.' berhasil dipanggil.');
    }

    /**
     * Tandai antrean aktif sebagai selesai (Completed).
     * POST /admin/papan-panggil/{booking}/complete
     */
    public function complete(Booking $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($booking->service->department_id !== $user->departments_id) {
            abort(403, 'Anda tidak berhak mengelola antrean instansi lain.');
        }

        $booking->update(['status' => 'Completed']);
        session()->forget('papan_panggil_active_booking_id');

        return back()->with('success', 'Antrean '.$booking->booking_code.' selesai dilayani.');
    }

    /**
     * Lewati/batalkan antrean aktif (Cancelled).
     * POST /admin/papan-panggil/{booking}/skip
     */
    public function skip(Request $request, Booking $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($booking->service->department_id !== $user->departments_id) {
            abort(403, 'Anda tidak berhak mengelola antrean instansi lain.');
        }

        $request->validate([
            'cancel_reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'cancel_reason.required' => 'Alasan pembatalan/melewati antrean harus diisi.',
            'cancel_reason.min' => 'Alasan pembatalan harus minimal 5 karakter.',
        ]);

        $booking->update([
            'status' => 'Cancelled',
            'cancel_reason' => $request->input('cancel_reason'),
        ]);

        session()->forget('papan_panggil_active_booking_id');

        return back()->with('success', 'Antrean '.$booking->booking_code.' berhasil dilewati.');
    }
}
