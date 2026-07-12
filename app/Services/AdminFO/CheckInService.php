<?php

declare(strict_types=1);

namespace App\Services\AdminFO;

use App\Enums\QueueStatus;
use App\Events\QueueCreated;
use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckInService
{
    /**
     * Cari booking berdasarkan kode booking atau NIK warga.
     */
    public function findBookingByCode(string $code): ?Queue
    {
        $code = trim($code);

        if (preg_match('/^\d{16}$/', $code)) {
            // 1. Prioritaskan booking yang masih berstatus 'Booked' (belum check-in/batal/selesai)
            $booking = Queue::whereHas('user', function ($query) use ($code) {
                $query->where('nik', $code);
            })
                ->where('status', QueueStatus::Booked->value)
                ->with(['user', 'department'])
                ->first();

            if ($booking) {
                return $booking;
            }

            // 2. Jika tidak ada yang 'Booked', cari booking terbaru dari NIK tersebut (untuk status warning)
            return Queue::whereHas('user', function ($query) use ($code) {
                $query->where('nik', $code);
            })
                ->latest('booking_date')
                ->with(['user', 'department'])
                ->first();
        }

        return Queue::where('booking_code', $code)
            ->with(['user', 'department'])
            ->first();
    }

    /**
     * Perbarui NIK user/warga.
     */
    public function updateCitizenNik(User $user, string $nikBaru, Queue $booking): void
    {
        $user->update(['nik' => $nikBaru]);

        ActivityLog::record(
            action: 'UPDATE_NIK',
            modelType: 'User',
            modelId: $user->id,
            description: "Admin FO mengisi NIK warga {$user->name} → {$nikBaru} saat proses check-in booking {$booking->booking_code}.",
            actorUserId: Auth::id(),
        );
    }

    /**
     * Setujui check-in booking.
     */
    public function approveCheckIn(Queue $booking): Queue
    {
        $today = Carbon::today()->toDateString();

        $queue = DB::transaction(function () use ($booking, $today) {
            $department = $booking->department;
            if (! $department) {
                throw new \Exception('Instansi/Department tidak ditemukan untuk booking ini.');
            }

            // 1. Validasi kuota harian (REQ-2.3 & BR 5)
            $maxQuota = (int) (Setting::getVal('daily_quota') ?? Setting::getVal('daily_quota_limit') ?? 100);
            $todayActiveCount = Queue::where('department_id', $booking->department_id)
                ->whereDate('booking_date', $today)
                ->whereNotNull('queue_number')
                ->count();

            if ($todayActiveCount >= $maxQuota) {
                throw new \Exception('Kuota layanan untuk hari ini telah penuh');
            }

            // 2. Validasi 1 NIK 1 Antrean Aktif (REQ-1.5 & BR 5)
            if ($booking->user && $booking->user->nik) {
                $existingActiveQueue = Queue::whereHas('user', function ($query) use ($booking) {
                    $query->where('nik', $booking->user->nik);
                })
                    ->where('department_id', $booking->department_id)
                    ->whereDate('booking_date', $today)
                    ->whereIn('status', [QueueStatus::CheckedIn->value, QueueStatus::Serving->value])
                    ->where('id', '!=', $booking->id)
                    ->exists();

                if ($existingActiveQueue) {
                    throw new \Exception('Warga dengan NIK ini sudah memiliki antrean aktif hari ini untuk instansi yang sama.');
                }
            }

            $queueNumber = $this->generateQueueNumber($department, $today, (bool) $booking->is_priority);

            // Update status + set booking_date ke hari ini (tanggal check-in sebenarnya)
            // agar query gerai (whereDate('booking_date', today)) dapat menemukan antrean ini.
            $booking->update([
                'status' => QueueStatus::CheckedIn->value,
                'checked_in_at' => now(),
                'queue_number' => $queueNumber,
                'booking_date' => $today,  // ← pastikan booking_date = hari check-in
            ]);

            ActivityLog::record(
                action: 'VERIFY_CHECKIN',
                modelType: 'Booking',
                modelId: $booking->id,
                description: "Admin FO berhasil menyetujui dokumen & check-in booking {$booking->booking_code} atas nama {$booking->user->name}. Nomor antrean {$queueNumber} diterbitkan.",
                actorUserId: Auth::id(),
            );

            return $booking;
        });

        event(new QueueCreated($queue));

        return $queue;
    }

    /**
     * Tolak check-in booking (batalkan).
     */
    public function rejectCheckIn(Queue $booking, string $reason): void
    {
        DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => QueueStatus::Cancelled->value,
                'cancel_reason' => $reason,
            ]);

            Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Booking Ditolak FO',
                'message' => "Reservasi antrean untuk layanan {$booking->purpose} pada {$booking->booking_date->translatedFormat('d F Y')} ditolak oleh petugas Front Office dengan alasan: {$reason}.",
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
    }

    /**
     * Generate queue number berdasarkan department & tanggal.
     */
    public function generateQueueNumber(Department $department, string $today, bool $isPriority = false): string
    {
        $queueNumbers = Queue::where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('is_priority', $isPriority)
            ->whereNotNull('queue_number')
            ->lockForUpdate()
            ->pluck('queue_number')
            ->map(function ($num) {
                $parts = explode('-', $num);

                return (int) end($parts);
            });

        $nextNumber = $queueNumbers->isEmpty() ? 1 : $queueNumbers->max() + 1;
        $prefix = $isPriority ? 'P' : ($department->inisial ?: 'Q');

        return $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
