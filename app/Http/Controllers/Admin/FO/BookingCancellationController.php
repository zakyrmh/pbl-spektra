<?php

namespace App\Http\Controllers\Admin\FO;

use App\Http\Controllers\Controller;
use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingCancellationController extends Controller
{
    /**
     * Tampilkan semua daftar booking online yang berstatus 'Pending'.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));

        $query = Booking::where('status', 'Pending')
            ->with(['user', 'service.department', 'schedule']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->orderBy('booking_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.fo.bookings.index', [
            'bookings' => $bookings,
            'search' => $search,
        ]);
    }

    /**
     * Proses pembatalan booking oleh Admin FO dengan memberikan alasan.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $userRole = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        if (! in_array($userRole, ['admin_fo', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk membatalkan booking ini.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'reason.required' => 'Alasan pembatalan wajib diisi.',
            'reason.min' => 'Alasan pembatalan minimal harus 5 karakter.',
            'reason.max' => 'Alasan pembatalan maksimal 255 karakter.',
        ]);

        $reason = trim($request->input('reason'));

        try {
            DB::transaction(function () use ($booking, $reason, $user) {
                $booking->update([
                    'status' => 'Cancelled',
                    'cancel_reason' => $reason,
                ]);

                // Buat notifikasi sistem untuk customer
                Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'Booking Dibatalkan oleh FO',
                    'message' => "Reservasi antrean untuk layanan {$booking->service->name} pada {$booking->booking_date->translatedFormat('d F Y')} dibatalkan oleh petugas Front Office dengan alasan: {$reason}.",
                ]);

                // Catat activity log
                ActivityLog::record(
                    action: 'CANCEL_BOOKING',
                    modelType: 'Booking',
                    modelId: $booking->id,
                    description: "Petugas FO '{$user->name}' membatalkan booking {$booking->booking_code} milik {$booking->user->name} dengan alasan: {$reason}.",
                    actorUserId: $user->id
                );
            });

            // Kirim email (SMTP error tidak boleh membatalkan DB transaction)
            try {
                Mail::to($booking->user->email)->send(new BookingCancelledMail($booking));
            } catch (\Exception $e) {
                Log::warning("CANCEL_BOOKING: Gagal mengirim email pembatalan ke {$booking->user->email} untuk booking {$booking->booking_code}: ".$e->getMessage());
            }

            return redirect()
                ->route('admin.fo.bookings.index')
                ->with('success', "Booking <strong>{$booking->booking_code}</strong> atas nama <strong>{$booking->user->name}</strong> berhasil dibatalkan.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal memproses pembatalan: '.$e->getMessage()]);
        }
    }
}
