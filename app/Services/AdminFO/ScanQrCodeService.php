<?php

declare(strict_types=1);

namespace App\Services\AdminFO;

use App\Enums\QueueStatus;
use App\Events\QueueCreated;
use App\Models\ActivityLog;
use App\Models\Queue;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScanQrCodeService
{
    /**
     * Process scanned QR Code to update queue status to 'hadir' (Checked-In) or 'diproses' (Serving).
     *
     * @throws \Exception
     */
    public function processQrCode(string $code, string $targetStatus = 'Checked-In'): Queue
    {
        $code = trim($code);

        // NIK is 16 digits
        if (preg_match('/^\d{16}$/', $code)) {
            $queue = Queue::whereHas('user', function ($query) use ($code) {
                $query->where('nik', $code);
            })
                ->with(['user', 'department'])
                ->first();
        } else {
            $queue = Queue::where('booking_code', $code)
                ->with(['user', 'department'])
                ->first();
        }

        if (! $queue) {
            throw new \Exception('Tiket/booking tidak ditemukan.');
        }

        $today = Carbon::today()->toDateString();

        return DB::transaction(function () use ($queue, $today, $targetStatus) {
            $department = $queue->department;
            if (! $department) {
                throw new \Exception('Instansi/Department tidak ditemukan untuk antrean ini.');
            }

            // If we are checking in (hadir)
            if ($targetStatus === 'Checked-In') {
                if ($queue->status->value === 'Checked-In' || $queue->status === 'Checked-In') {
                    throw new \Exception('Tiket ini sudah melakukan check-in.');
                }

                $currentStatus = $queue->status->value ?? $queue->status;
                if (! in_array($currentStatus, ['Booked', 'Pending'])) {
                    throw new \Exception('Tiket tidak dalam status pending/booked (Status saat ini: '.$currentStatus.').');
                }

                // 1. Validasi kuota harian (REQ-2.3 & BR 5)
                $maxQuota = (int) (Setting::getVal('daily_quota') ?? Setting::getVal('daily_quota_limit') ?? 100);
                $todayActiveCount = Queue::where('department_id', $queue->department_id)
                    ->whereDate('booking_date', $today)
                    ->whereNotNull('queue_number')
                    ->count();

                if ($todayActiveCount >= $maxQuota) {
                    throw new \Exception('Kuota layanan untuk hari ini telah penuh');
                }

                // 2. Validasi 1 NIK 1 Antrean Aktif (REQ-1.5 & BR 5)
                if ($queue->user && $queue->user->nik) {
                    $hasActive = Queue::whereHas('user', function ($query) use ($queue) {
                        $query->where('nik', $queue->user->nik);
                    })
                        ->where('department_id', $queue->department_id)
                        ->whereDate('booking_date', $today)
                        ->whereIn('status', ['Checked-In', 'Serving'])
                        ->where('id', '!=', $queue->id)
                        ->exists();

                    if ($hasActive) {
                        throw new \Exception('Warga dengan NIK ini sudah memiliki antrean aktif hari ini untuk instansi yang sama.');
                    }
                }

                // Generate Queue Number
                $queueNumber = $this->generateQueueNumberForDept($department->id, $department->inisial ?: 'Q', $today);

                // Update queue
                $queue->status = QueueStatus::CheckedIn;
                $queue->checked_in_at = now();
                $queue->queue_number = $queueNumber;
                $queue->booking_date = Carbon::parse($today);

                $queue->save();

                ActivityLog::record(
                    action: 'VERIFY_CHECKIN_QR',
                    modelType: 'Booking',
                    modelId: $queue->id,
                    description: "Scan QR Code: Admin FO menyetujui check-in booking {$queue->booking_code} atas nama {$queue->user->name}. Nomor antrean {$queueNumber} diterbitkan.",
                    actorUserId: Auth::id(),
                );

                event(new QueueCreated($queue));

            } elseif ($targetStatus === 'Serving') {
                // If setting status to diproses (Serving)
                if ($queue->status->value === 'Serving' || $queue->status === 'Serving') {
                    throw new \Exception('Antrean ini sudah dalam proses pelayanan.');
                }

                $queue->status = QueueStatus::Serving;
                $queue->save();

                ActivityLog::record(
                    action: 'SERVE_QUEUE_QR',
                    modelType: 'Queue',
                    modelId: $queue->id,
                    description: "Scan QR Code: Status antrean {$queue->queue_number} diubah menjadi Diproses (Serving).",
                    actorUserId: Auth::id(),
                );
            } else {
                throw new \Exception("Status target '{$targetStatus}' tidak didukung.");
            }

            return $queue;
        });
    }

    /**
     * Generate queue number berdasarkan department & date.
     */
    protected function generateQueueNumberForDept(int $departmentId, string $prefix, string $today): string
    {
        $queueNumbers = Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $today)
            ->whereNotNull('queue_number')
            ->pluck('queue_number');

        $nums = $queueNumbers->map(function ($num) {
            $parts = explode('-', $num);

            return (int) end($parts);
        });

        $nextNumber = $nums->isEmpty() ? 1 : $nums->max() + 1;

        return $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
