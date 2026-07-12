<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\QueueStatus;
use App\Mail\BookingMovedMail;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MoveSessionBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:move-session';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Otomatis memindahkan booking Sesi 1 yang belum check-in ke Sesi 2 jika Sesi 2 tidak ramai pada jam 12:00';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        // 1. Cari booking hari ini berstatus Booked (belum check-in) di Sesi 1
        $bookings = Queue::whereDate('booking_date', $today)
            ->where('session_name', 'Sesi 1')
            ->where('status', QueueStatus::Booked->value ?? QueueStatus::Booked)
            ->with(['user', 'department'])
            ->get();

        $count = $bookings->count();

        if ($count === 0) {
            $this->info('Tidak ada booking Pending Sesi 1 untuk diproses hari ini.');

            return 0;
        }

        $this->info("Menemukan {$count} booking Sesi 1 yang belum check-in. Memproses pemindahan...");

        $successCount = 0;
        $skippedCount = 0;

        foreach ($bookings as $booking) {
            try {
                // Ambil batas kuota harian
                $maxQuota = (int) (Setting::getVal('daily_quota') ?? Setting::getVal('daily_quota_limit') ?? 100);
                $session2Limit = (int) ($maxQuota / 2);

                // Hitung antrean aktif di Sesi 2 untuk instansi/department terkait hari ini
                $session2Count = Queue::where('department_id', $booking->department_id)
                    ->whereDate('booking_date', $today)
                    ->where('session_name', 'Sesi 2')
                    ->whereIn('status', [
                        QueueStatus::Booked->value ?? 'Booked',
                        QueueStatus::CheckedIn->value ?? 'Checked-In',
                        QueueStatus::Serving->value ?? 'Serving',
                        QueueStatus::Hold->value ?? 'Hold',
                    ])
                    ->count();

                // Check jika Sesi 2 tidak ramai
                if ($session2Count < $session2Limit) {
                    DB::transaction(function () use ($booking) {
                        $booking->update([
                            'session_name' => 'Sesi 2',
                        ]);

                        // Kirim notifikasi sistem
                        Notification::create([
                            'user_id' => $booking->user_id,
                            'title' => 'Pemindahan Sesi Antrean',
                            'message' => "Reservasi antrean untuk layanan {$booking->purpose} pada {$booking->booking_date->translatedFormat('d F Y')} otomatis dipindahkan ke Sesi 2 karena tidak dilakukan check-in pada Sesi 1.",
                        ]);

                        // Catat activity log
                        ActivityLog::record(
                            action: 'MOVE_SESSION',
                            modelType: 'Booking',
                            modelId: $booking->id,
                            description: "Sistem otomatis memindahkan booking {$booking->booking_code} ke Sesi 2 karena tidak check-in pada Sesi 1.",
                            actorUserId: null
                        );
                    });

                    // Kirim email (SMTP error tidak boleh membatalkan DB transaction)
                    try {
                        Mail::to($booking->user->email)->send(new BookingMovedMail($booking));
                    } catch (\Exception $e) {
                        Log::warning("MOVE_SESSION: Gagal mengirim email ke {$booking->user->email} untuk booking {$booking->booking_code}: ".$e->getMessage());
                    }

                    $successCount++;
                } else {
                    $this->info("Booking {$booking->booking_code} dilewati karena Sesi 2 instansi {$booking->department->name} sudah ramai ({$session2Count}/{$session2Limit}).");
                    $skippedCount++;
                }
            } catch (\Exception $e) {
                Log::error("MOVE_SESSION: Gagal memproses pemindahan booking {$booking->booking_code}: ".$e->getMessage());
                $this->error("Gagal memproses booking: {$booking->booking_code}");
            }
        }

        $this->info("Selesai! {$successCount} booking berhasil dipindahkan ke Sesi 2, {$skippedCount} booking dilewati karena kuota penuh.");

        return 0;
    }
}
