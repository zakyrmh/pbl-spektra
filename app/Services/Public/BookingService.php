<?php

declare(strict_types=1);

namespace App\Services\Public;

use App\Enums\QueueStatus;
use App\Mail\BookingSuccessMail;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Get customer booking/queue history with department details.
     *
     * @return Collection<int, Queue>
     */
    public function getCustomerBookingHistory(int $userId): Collection
    {
        return Queue::where('user_id', $userId)
            ->with('department')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Process creation of a booking/queue for a user inside a database transaction.
     *
     * @param  array{department_id: int|string, keperluan: string, booking_date: string, session_name: string}  $data
     *
     * @throws \Exception
     */
    public function processBookingCreation(int $userId, array $data): Queue
    {
        $user = User::findOrFail($userId);

        $booking = DB::transaction(function () use ($user, $data): Queue {
            $department = Department::findOrFail((int) $data['department_id']);
            $bookingDate = Carbon::parse($data['booking_date']);

            if (! $department->is_open) {
                throw new \Exception('Instansi terpilih saat ini sedang ditutup.');
            }

            // Hanya boleh ada maksimal 1 tiket aktif di seluruh tanggal
            $activeBooking = Queue::where('user_id', $user->id)
                ->whereIn('status', [
                    QueueStatus::Booked->value,
                    QueueStatus::CheckedIn->value,
                    QueueStatus::Serving->value,
                    QueueStatus::Hold->value,
                ])
                ->first();

            if ($activeBooking) {
                throw new \Exception("Anda masih memiliki tiket/booking aktif ({$activeBooking->booking_code}). Harap selesaikan pelayanan atau batalkan tiket aktif tersebut terlebih dahulu sebelum membuat booking baru.");
            }

            $prefix = $department->inisial ?: 'Q';

            // Generate structured booking code
            $dateStr = $bookingDate->format('Ymd');
            $bookingCode = 'BK-'.$prefix.'-'.$dateStr.'-'.strtoupper(Str::random(6));

            // Create booking/queue
            $booking = Queue::create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'booking_code' => $bookingCode,
                'purpose' => $data['keperluan'],
                'session_name' => $data['session_name'],
                'booking_date' => $bookingDate->toDateString(),
                'queue_number' => null,
                'status' => 'Booked',
                'is_priority' => (bool) $user->is_priority,
            ]);

            // Save notifications for customer
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Booking Antrean Berhasil',
                'message' => "Reservasi antrean untuk instansi {$department->name} pada {$bookingDate->translatedFormat('d F Y')} berhasil dibuat dengan kode {$bookingCode}.",
            ]);

            // Save notifications for all FO Admins (real-time data)
            $foAdmins = User::where('role', 'admin_fo')->get();
            foreach ($foAdmins as $fo) {
                Notification::create([
                    'user_id' => $fo->id,
                    'title' => 'Booking Baru Masuk',
                    'message' => "Pengunjung {$user->name} membuat booking online baru: {$bookingCode} (Instansi: {$department->name}).",
                ]);
            }

            // Record activity log
            ActivityLog::record(
                action: 'CREATE_BOOKING',
                modelType: 'Queue',
                modelId: $booking->id,
                description: "Pengunjung '{$user->name}' membuat booking online dengan kode {$bookingCode} tujuan instansi {$department->name} tanggal {$bookingDate->toDateString()}.",
                actorUserId: $user->id
            );

            return $booking;
        });

        // Send email notification to citizen
        try {
            Mail::to($user->email)->send(new BookingSuccessMail($booking));
        } catch (\Exception $e) {
            // Log warning, but do not fail the booking flow
            logger()->warning('Gagal mengirim email konfirmasi booking: '.$e->getMessage(), [
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        return $booking;
    }

    /**
     * Calculate the real-time queue position for a booking.
     */
    public function calculateEstimatedPosition(Queue $booking): int
    {
        return Queue::where('department_id', $booking->department_id)
            ->whereDate('booking_date', $booking->booking_date->toDateString())
            ->where('id', '<', $booking->id)
            ->whereIn('status', [QueueStatus::Booked->value, QueueStatus::CheckedIn->value])
            ->count() + 1;
    }

    /**
     * Periksa apakah pengguna memiliki booking/antrean aktif.
     */
    public function hasActiveBooking(int $userId): bool
    {
        return Queue::where('user_id', $userId)
            ->whereIn('status', [
                QueueStatus::Booked->value,
                QueueStatus::CheckedIn->value,
                QueueStatus::Serving->value,
                QueueStatus::Hold->value,
            ])
            ->exists();
    }
}
