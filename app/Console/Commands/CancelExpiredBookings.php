<?php

namespace App\Console\Commands;

use App\Enums\QueueStatus;
use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Queue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

#[Signature('bookings:cancel-expired')]
#[Description('Otomatis membatalkan booking Pending yang sudah kadaluarsa (melewati hari pelayanan)')]
class CancelExpiredBookings extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        $expiredBookings = Queue::where('status', QueueStatus::Booked)
            ->whereDate('booking_date', '<', $today)
            ->with(['user', 'department'])
            ->get();

        $count = $expiredBookings->count();

        if ($count === 0) {
            $this->info('Tidak ada booking pending yang kadaluarsa hari ini.');

            return 0;
        }

        $this->info("Menemukan {$count} booking pending kadaluarsa. Memulai proses pembatalan otomatis...");

        $successCount = 0;

        foreach ($expiredBookings as $booking) {
            try {
                DB::transaction(function () use ($booking) {
                    $booking->update([
                        'status' => QueueStatus::Cancelled,
                        'cancel_reason' => 'Kadaluarsa',
                    ]);

                    // Kirim notifikasi sistem
                    Notification::create([
                        'user_id' => $booking->user_id,
                        'title' => 'Booking Kadaluarsa',
                        'message' => "Reservasi antrean untuk layanan {$booking->purpose} pada {$booking->booking_date->translatedFormat('d F Y')} otomatis dibatalkan karena tidak dilakukan check-in.",
                    ]);

                    // Catat activity log
                    ActivityLog::record(
                        action: 'AUTO_CANCEL',
                        modelType: 'Booking',
                        modelId: $booking->id,
                        description: "Sistem otomatis membatalkan booking {$booking->booking_code} karena tidak check-in pada hari pelayanan.",
                        actorUserId: null
                    );
                });

                // Kirim email (SMTP error tidak boleh membatalkan DB transaction)
                try {
                    Mail::to($booking->user->email)->send(new BookingCancelledMail($booking));
                } catch (\Exception $e) {
                    Log::warning("AUTO_CANCEL: Gagal mengirim email ke {$booking->user->email} untuk booking {$booking->booking_code}: ".$e->getMessage());
                }

                $successCount++;
            } catch (\Exception $e) {
                Log::error("AUTO_CANCEL: Gagal memproses pembatalan booking {$booking->booking_code}: ".$e->getMessage());
                $this->error("Gagal memproses booking: {$booking->booking_code}");
            }
        }

        $this->info("Selesai! {$successCount} dari {$count} booking berhasil dibatalkan otomatis.");

        return 0;
    }
}
