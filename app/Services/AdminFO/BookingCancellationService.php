<?php

declare(strict_types=1);

namespace App\Services\AdminFO;

use App\Enums\QueueStatus;
use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingCancellationService
{
    /**
     * Cari booking pending/booked berdasarkan filter pencarian.
     *
     * @return Collection<int, Queue>
     */
    public function getPendingBookings(?string $search = null): Collection
    {
        // Ganti status 'Pending' ke 'Booked' (karena Booking model dihapus dan online booking status di queues adalah 'Booked')
        $query = Queue::where('status', QueueStatus::Booked->value)
            ->with(['user', 'department']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('booking_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Batalkan booking.
     */
    public function cancelBooking(Queue $booking, string $reason, User $actor): void
    {
        DB::transaction(function () use ($booking, $reason, $actor) {
            $booking->update([
                'status' => QueueStatus::Cancelled->value,
                'cancel_reason' => $reason,
            ]);

            // Buat notifikasi sistem untuk customer
            Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Booking Dibatalkan oleh FO',
                'message' => "Reservasi antrean untuk layanan {$booking->purpose} pada {$booking->booking_date->translatedFormat('d F Y')} dibatalkan oleh petugas Front Office dengan alasan: {$reason}.",
            ]);

            // Catat activity log
            ActivityLog::record(
                action: 'CANCEL_BOOKING',
                modelType: 'Booking',
                modelId: $booking->id,
                description: "Petugas FO '{$actor->name}' membatalkan booking {$booking->booking_code} milik {$booking->user->name} dengan alasan: {$reason}.",
                actorUserId: $actor->id
            );
        });

        // Kirim email
        try {
            Mail::to($booking->user->email)->send(new BookingCancelledMail($booking));
        } catch (\Exception $e) {
            Log::warning("CANCEL_BOOKING: Gagal mengirim email pembatalan ke {$booking->user->email} untuk booking {$booking->booking_code}: ".$e->getMessage());
        }
    }
}
