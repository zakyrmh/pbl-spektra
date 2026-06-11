<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO;

use App\Events\QueueCreated;
use App\Http\Controllers\Controller;
use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * CheckInController — Verifikasi & Check-In Booking Online (Web).
 *
 * Hanya menangani halaman web FO:
 *   GET  /fo/check-in            → index
 *   POST /fo/check-in/verify     → verify
 *   POST /fo/check-in/{booking}/approve → approve
 *   POST /fo/check-in/{booking}/reject  → reject
 *
 * REST API endpoint dipindahkan ke Admin\FO\Api\CheckInApiController.
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
     * Verifikasi kode booking & tampilkan detail untuk persetujuan.
     * POST /fo/check-in/verify
     */
    public function verify(Request $request)
    {
        $request->validate([
            'booking_code' => ['required', 'string', 'max:36'],
            'nik_input' => ['nullable', 'string', 'digits:16'],
        ]);

        $code = trim($request->input('booking_code'));

        // 1. Cari booking
        $booking = Booking::where('booking_code', $code)
            ->with(['user', 'service.department', 'schedule'])
            ->first();

        if (! $booking) {
            return back()
                ->withInput()
                ->with('error', "Kode booking <strong>{$code}</strong> tidak ditemukan. Pastikan kode yang Anda masukkan sudah benar.")
                ->with('searched_code', $code);
        }

        // 2. Validasi status booking
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

        // 3. Cek NIK warga
        $user = $booking->user;

        if (empty($user->nik)) {
            if ($request->filled('nik_input')) {
                $nikBaru = $request->input('nik_input');

                if (User::where('nik', $nikBaru)->where('id', '!=', $user->id)->exists()) {
                    session()->flash('error', "NIK <strong>{$nikBaru}</strong> sudah terdaftar di sistem untuk pengguna lain.");

                    return view('admin.fo.checkin', ['booking' => $booking, 'nik_required' => true]);
                }

                $user->update(['nik' => $nikBaru]);

                ActivityLog::record(
                    action: 'UPDATE_NIK',
                    modelType: 'User',
                    modelId: $user->id,
                    description: "Admin FO mengisi NIK warga {$user->name} → {$nikBaru} saat proses check-in booking {$booking->booking_code}.",
                    actorUserId: Auth::id(),
                );
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
    public function approve(Request $request, Booking $booking)
    {
        if (! $booking->canBeCheckedIn()) {
            return redirect()->route('admin.fo.checkin')
                ->with('warning', 'Booking ini tidak dapat diproses.');
        }

        if (empty($booking->user->nik)) {
            return redirect()->route('admin.fo.checkin')
                ->with('booking', $booking)
                ->with('nik_required', true)
                ->with('error', 'NIK wajib diisi sebelum menyetujui check-in.');
        }

        $today = now()->toDateString();

        try {
            $queue = DB::transaction(function () use ($booking, $today) {
                $booking->update([
                    'status' => 'Checked-In',
                    'checked_in_at' => now(),
                ]);

                $counter = Counter::where('department_id', $booking->service->department_id)->first();
                if (! $counter) {
                    throw new \Exception('Belum ada loket/counter yang terdaftar untuk instansi '.$booking->service->department->name.'.');
                }

                $queueNumber = $this->generateQueueNumber($counter, $today);

                $queue = Queue::create([
                    'booking_id' => $booking->id,
                    'visitor_id' => null,
                    'counter_id' => $counter->id,
                    'service_id' => $booking->service_id,
                    'queue_number' => $queueNumber,
                    'status' => 'Waiting',
                    'queue_date' => $today,
                ]);

                ActivityLog::record(
                    action: 'VERIFY_CHECKIN',
                    modelType: 'Booking',
                    modelId: $booking->id,
                    description: "Admin FO berhasil menyetujui dokumen & check-in booking {$booking->booking_code} atas nama {$booking->user->name}. Nomor antrean {$queueNumber} diterbitkan.",
                    actorUserId: Auth::id(),
                );

                return $queue;
            });

            event(new QueueCreated($queue));

            return redirect()->route('admin.fo.checkin')
                ->with('success', "Check-in berhasil! Warga <strong>{$booking->user->name}</strong> telah terverifikasi.")
                ->with('checkin_result', $booking->fresh(['user', 'queue.counter.department', 'service']));
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
    public function reject(Request $request, Booking $booking)
    {
        if (! $booking->canBeCheckedIn()) {
            return redirect()->route('admin.fo.checkin')
                ->with('warning', 'Booking ini tidak dapat diproses.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi.',
            'reason.min' => 'Alasan penolakan minimal harus 5 karakter.',
            'reason.max' => 'Alasan penolakan maksimal 255 karakter.',
        ]);

        $reason = trim($request->input('reason'));

        try {
            DB::transaction(function () use ($booking, $reason) {
                $booking->update([
                    'status' => 'Cancelled',
                    'cancel_reason' => $reason,
                ]);

                Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'Booking Ditolak FO',
                    'message' => "Reservasi antrean untuk layanan {$booking->service->name} pada {$booking->booking_date->translatedFormat('d F Y')} ditolak oleh petugas Front Office dengan alasan: {$reason}.",
                ]);

                ActivityLog::record(
                    action: 'REJECT_BOOKING',
                    modelType: 'Booking',
                    modelId: $booking->id,
                    description: "Admin FO menolak booking {$booking->booking_code} milik {$booking->user->name} karena dokumen fisik tidak lengkap/tidak sesuai. Alasan: {$reason}.",
                    actorUserId: Auth::id(),
                );
            });

            try {
                Mail::to($booking->user->email)->send(new BookingCancelledMail($booking));
            } catch (\Exception $e) {
                Log::warning("REJECT_BOOKING: Gagal mengirim email ke {$booking->user->email} untuk booking {$booking->booking_code}: ".$e->getMessage());
            }

            return redirect()->route('admin.fo.checkin')
                ->with('success', "Booking <strong>{$booking->booking_code}</strong> atas nama <strong>{$booking->user->name}</strong> berhasil ditolak.");
        } catch (\Exception $e) {
            return redirect()->route('admin.fo.checkin')
                ->withInput()
                ->with('error', 'Gagal memproses penolakan: '.$e->getMessage());
        }
    }

    /**
     * Generate queue number berdasarkan counter & tanggal.
     */
    private function generateQueueNumber(Counter $counter, string $today): string
    {
        $existingCount = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->lockForUpdate()
            ->count();

        $prefix = $counter->department->inisial ?: 'Q';

        return $prefix.'-'.str_pad((string) ($existingCount + 1), 3, '0', STR_PAD_LEFT);
    }
}
