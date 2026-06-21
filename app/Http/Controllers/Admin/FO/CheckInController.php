<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO;

use App\Enums\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFO\RejectCheckInRequest;
use App\Http\Requests\AdminFO\VerifyCheckInRequest;
use App\Models\Queue;
use App\Models\User;
use App\Services\AdminFO\CheckInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CheckInController — Verifikasi & Check-In Booking Online (Web).
 *
 * Hanya menangani halaman web FO:
 *   GET  /fo/check-in            → index
 *   POST /fo/check-in/verify     → verify
 *   POST /fo/check-in/{booking}/approve → approve
 *   POST /fo/check-in/{booking}/reject  → reject
 */
final class CheckInController extends Controller
{
    public function __construct(
        protected CheckInService $checkInService
    ) {}

    /**
     * Halaman utama check-in: tampilkan form pencarian kode booking.
     * GET /fo/check-in
     */
    public function index(): View
    {
        return view('admin.fo.checkin');
    }

    /**
     * Verifikasi kode booking & tampilkan detail untuk persetujuan.
     * POST /fo/check-in/verify
     */
    public function verify(VerifyCheckInRequest $request): View|RedirectResponse
    {
        $code = trim($request->input('booking_code'));

        // 1. Cari booking
        $booking = $this->checkInService->findBookingByCode($code);

        if (! $booking) {
            return back()
                ->withInput()
                ->with('error', "Kode booking <strong>{$code}</strong> tidak ditemukan. Pastikan kode yang Anda masukkan sudah benar.")
                ->with('searched_code', $code);
        }

        // 2. Validasi status booking
        if ($booking->status !== QueueStatus::Booked->value && $booking->status !== QueueStatus::Booked) {
            $statusLabel = match ($booking->status->value ?? $booking->status) {
                'Checked-In' => 'sudah di-check-in sebelumnya',
                'Completed' => 'sudah selesai dilayani',
                'Cancelled' => 'telah dibatalkan',
                default => "berstatus {$booking->status}",
            };

            return back()
                ->withInput()
                ->with('warning', "Booking ini tidak dapat diproses karena {$statusLabel}.");
        }

        // 3. Cek NIK warga
        $user = $booking->user;

        if (empty($user->nik)) {
            if ($request->filled('nik_input')) {
                $nikBaru = $request->input('nik_input');

                if (User::where('nik', $nikBaru)->where('id', '!=', $user->id)->exists()) {
                    session()->flash('error', "NIK <strong>{$nikBaru}</strong> sudah terdaftar di sistem untuk pengguna lain.");

                    return view('admin.fo.checkin', ['booking' => $booking, 'nik_required' => true]);
                }

                $this->checkInService->updateCitizenNik($user, $nikBaru, $booking);
            } else {
                return view('admin.fo.checkin', ['booking' => $booking, 'nik_required' => true]);
            }
        }

        // 4. Tampilkan halaman konfirmasi
        return view('admin.fo.checkin', ['booking' => $booking, 'nik_required' => false]);
    }

    /**
     * Setujui verifikasi dokumen dan terbitkan nomor antrean.
     * POST /fo/check-in/{booking}/approve
     */
    public function approve(Request $request, Queue $booking): RedirectResponse
    {
        // Pastikan relasi user & department ter-load dengan benar
        $booking->loadMissing(['user', 'department']);

        if ($booking->status !== QueueStatus::Booked->value && $booking->status !== QueueStatus::Booked) {
            return redirect()->route('admin.fo.checkin')
                ->with('warning', 'Booking ini tidak dapat diproses.');
        }

        if (empty($booking->user->nik)) {
            // Simpan hanya booking_code ke session, bukan seluruh object Eloquent,
            // agar relasi tidak ter-serialize menjadi array saat diambil dari session.
            return redirect()->route('admin.fo.checkin')
                ->with('booking_code_pending', $booking->booking_code)
                ->with('nik_required', true)
                ->with('error', 'NIK wajib diisi sebelum menyetujui check-in.');
        }

        try {
            $queue = $this->checkInService->approveCheckIn($booking);

            // Simpan hanya ID queue ke session, lalu fetch fresh di view
            return redirect()->route('admin.fo.checkin')
                ->with('success', "Check-in berhasil! Warga <strong>{$booking->user->name}</strong> telah terverifikasi.")
                ->with('checkin_result', $queue->fresh(['user', 'department']));
        } catch (\Exception $e) {
            return redirect()->route('admin.fo.checkin')
                ->withInput()
                ->with('error', 'Gagal memproses check-in: '.$e->getMessage());
        }
    }

    /**
     * Tolak verifikasi dokumen warga (batalkan booking).
     * POST /fo/check-in/{booking}/reject
     */
    public function reject(RejectCheckInRequest $request, Queue $booking): RedirectResponse
    {
        if ($booking->status !== QueueStatus::Booked->value && $booking->status !== QueueStatus::Booked) {
            return redirect()->route('admin.fo.checkin')
                ->with('warning', 'Booking ini tidak dapat diproses.');
        }

        $reason = trim($request->input('reason'));

        try {
            $this->checkInService->rejectCheckIn($booking, $reason);

            return redirect()->route('admin.fo.checkin')
                ->with('success', "Booking <strong>{$booking->booking_code}</strong> atas nama <strong>{$booking->user->name}</strong> berhasil ditolak.");
        } catch (\Exception $e) {
            return redirect()->route('admin.fo.checkin')
                ->withInput()
                ->with('error', 'Gagal memproses penolakan: '.$e->getMessage());
        }
    }
}
