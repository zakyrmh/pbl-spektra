<?php

namespace App\Services;

use App\Mail\BookingSuccessMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Get available schedules for a given service.
     *
     * @return Collection
     */
    public function getAvailableSchedulesForService(int $serviceId)
    {
        return Schedule::where('service_id', $serviceId)
            ->where('date', '>=', now()->toDateString())
            ->where('is_open', true)
            ->whereColumn('quota_used', '<', 'quota_total')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Create a new booking for a user.
     *
     * @throws \Exception
     */
    public function createBooking(User $user, int $serviceId, int $scheduleId, ?string $purpose = null): Booking
    {
        // Execute booking creation inside a transaction
        $booking = DB::transaction(function () use ($user, $serviceId, $scheduleId, $purpose) {
            // Lock schedule for update to prevent concurrent double-booking of last slot
            $schedule = Schedule::where('id', $scheduleId)->lockForUpdate()->first();

            if (! $schedule) {
                throw new \Exception('Jadwal pelayanan terpilih tidak ditemukan.');
            }

            if (! $schedule->is_open) {
                throw new \Exception('Jadwal pelayanan terpilih sedang ditutup.');
            }

            if ($schedule->isFull()) {
                throw new \Exception('Kuota layanan pada jadwal terpilih sudah penuh.');
            }

            // BR-06: Satu NIK = maks 1 booking aktif (Pending) per layanan per hari
            $existingBooking = Booking::where('user_id', $user->id)
                ->where('service_id', $serviceId)
                ->whereDate('booking_date', $schedule->date->toDateString())
                ->where('status', 'Pending')
                ->exists();

            if ($existingBooking) {
                throw new \Exception('Anda sudah memiliki booking aktif (Pending) untuk layanan ini pada tanggal tersebut.');
            }

            // Generate UUID booking code
            $bookingCode = (string) Str::uuid();

            // Create booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'service_id' => $serviceId,
                'schedule_id' => $scheduleId,
                'purpose' => $purpose,
                'booking_code' => $bookingCode,
                'status' => 'Pending',
                'booking_date' => $schedule->date,
            ]);

            // Increment quota
            $schedule->increment('quota_used');

            // Save notifications for customer
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Booking Antrean Berhasil',
                'message' => "Reservasi antrean untuk layanan {$booking->service->name} pada {$booking->booking_date->translatedFormat('d F Y')} berhasil dibuat dengan kode {$bookingCode}.",
            ]);

            // Save notifications for all FO Admins (real-time data)
            $foAdmins = User::where('role', 'admin_fo')->get();
            foreach ($foAdmins as $fo) {
                Notification::create([
                    'user_id' => $fo->id,
                    'title' => 'Booking Baru Masuk',
                    'message' => "Pengunjung {$user->name} membuat booking online baru: {$bookingCode} (Layanan: {$booking->service->name}).",
                ]);
            }

            // Record activity log
            ActivityLog::record(
                action: 'CREATE_BOOKING',
                modelType: 'Booking',
                modelId: $booking->id,
                description: "Pengunjung '{$user->name}' membuat booking online dengan kode {$bookingCode} tujuan layanan {$booking->service->name} tanggal {$booking->booking_date->toDateString()}.",
                actorUserId: $user->id
            );

            return $booking;
        });

        // BR-11: Kegagalan SMTP tidak boleh membatalkan proses booking (try-catch)
        try {
            Mail::to($user->email)->send(new BookingSuccessMail($booking));
        } catch (\Exception $e) {
            Log::warning("SMTP Mail sending failed for booking {$booking->booking_code}: ".$e->getMessage());
        }

        return $booking;
    }
}
