<?php

namespace App\Http\Controllers\Admin\FO;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CheckInController — Verifikasi & Check-In Booking Online
 *
 * Rute   : admin.fo.checkin        → GET  /fo/check-in
 * Rute   : admin.fo.checkin.verify → POST /fo/check-in/verify
 *
 * Business Rules (AGENT.md §7):
 *  BR-02 Nomor antrian aktif hanya diterbitkan oleh front_office.
 *  REQ-2.1 FO dapat mencari booking berdasarkan NIK atau booking_code.
 */
class CheckInController extends Controller
{
    /**
     * Halaman utama check-in: tampilkan form pencarian kode booking.
     * GET /fo/check-in
     */
    public function index()
    {
        return view('admin.fo.checkin');
    }

    /**
     * Verifikasi kode booking & lakukan check-in.
     * POST /fo/check-in/verify
     *
     * Request fields:
     *  - booking_code : string — UUID atau kode pendek booking
     *  - nik_input    : string|nullable — NIK yang diisikan FO jika warga belum punya NIK
     */
    public function verify(Request $request)
    {
        $request->validate([
            'booking_code' => ['required', 'string', 'max:36'],
            'nik_input' => ['nullable', 'string', 'digits:16'],
        ]);

        $code = trim($request->input('booking_code'));

        // ── 1. Cari booking berdasarkan booking_code ──────────────────
        $booking = Booking::where('booking_code', $code)
            ->with('user')
            ->first();

        if (! $booking) {
            return back()
                ->withInput()
                ->with('error', "Kode booking <strong>{$code}</strong> tidak ditemukan. Pastikan kode yang Anda masukkan sudah benar.")
                ->with('searched_code', $code);
        }

        // ── 2. Validasi status booking ────────────────────────────────
        if (! $booking->canBeCheckedIn()) {
            $statusLabel = match ($booking->status) {
                'Checked-In' => 'sudah di-check-in sebelumnya',
                'Completed' => 'sudah selesai dilayani',
                'Cancelled' => 'telah dibatalkan',
                default => "berstatus {$booking->status}",
            };

            return back()
                ->withInput()
                ->with('warning', "Booking ini tidak dapat diproses karena {$statusLabel}.")
                ->with('booking', $booking);
        }

        // ── 3. Cek NIK warga — jika kosong, kembalikan ke view dengan flag ─
        $user = $booking->user;

        if (empty($user->nik)) {
            // Jika Admin FO sudah mengisi NIK via inline form, simpan dulu
            if ($request->filled('nik_input')) {
                $nikBaru = $request->input('nik_input');

                // Pastikan NIK belum dipakai user lain
                $nikSudahAda = User::where('nik', $nikBaru)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($nikSudahAda) {
                    return back()
                        ->withInput()
                        ->with('error', "NIK <strong>{$nikBaru}</strong> sudah terdaftar di sistem untuk pengguna lain.")
                        ->with('booking', $booking)
                        ->with('nik_required', true);
                }

                $user->update(['nik' => $nikBaru]);

                ActivityLog::record(
                    action     : 'UPDATE_NIK',
                    modelType  : 'User',
                    modelId    : $user->id,
                    description: "Admin FO ({$user->id}) mengisi NIK warga {$user->name} → {$nikBaru} saat proses check-in booking {$booking->booking_code}.",
                    actorUserId: Auth::id(),
                );
            } else {
                // NIK masih kosong & FO belum mengisi → kembalikan dengan flag untuk tampil form NIK
                return back()
                    ->withInput()
                    ->with('booking', $booking)
                    ->with('nik_required', true);
            }
        }

        // ── 4. Lakukan Check-In dalam satu transaksi ──────────────────
        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'Checked-In',
                'checked_in_at' => now(),
            ]);

            // Catat di activity_logs (AGENT.md §8 — Auditability)
            ActivityLog::record(
                action     : 'VERIFY_CHECKIN',
                modelType  : 'Booking',
                modelId    : $booking->id,
                description: "Admin FO berhasil check-in booking {$booking->booking_code} atas nama {$booking->user->name}.",
                actorUserId: Auth::id(),
            );
        });

        return redirect()->route('admin.fo.checkin')
            ->with('success', "Check-in berhasil! Warga <strong>{$booking->user->name}</strong> ({$booking->booking_code}) telah tercatat hadir.")
            ->with('checkin_result', $booking->fresh(['user']));
    }
}
