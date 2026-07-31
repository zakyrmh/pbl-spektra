<?php

declare(strict_types=1);

namespace App\Services\AdminFO;

use App\Data\AdminFO\WalkInTicketData;
use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Events\QueueCreated;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WalkInTicketService
{
    /**
     * Ambil data instansi (gerai) yang aktif untuk form pencetakan tiket.
     *
     * @return Collection<int, Department>
     */
    public function getFormData(): Collection
    {
        return Department::where('is_open', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Terbitkan nomor antrean walk-in menggunakan DB Transaction.
     * Menyimpan/memperbarui data pengguna dan menyimpan tiket ke queues secara atomik.
     *
     * @throws \Throwable
     */
    public function issueTicket(WalkInTicketData $data): Queue
    {
        $today = Carbon::today();

        // 1. Validasi kuota harian (REQ-2.3 & BR 5)
        $maxQuota = (int) (Setting::getVal('daily_quota') ?? Setting::getVal('daily_quota_limit') ?? 100);
        $todayActiveCount = Queue::where('department_id', $data->departmentId)
            ->whereDate('booking_date', $today)
            ->whereNotNull('queue_number')
            ->count();

        if ($todayActiveCount >= $maxQuota) {
            throw ValidationException::withMessages([
                'department_id' => 'Kuota layanan untuk hari ini telah penuh',
            ]);
        }

        // 1. Validasi duplikasi antrean aktif hari ini untuk instansi tujuan (jika NIK diisi)
        if ($data->nik) {
            $existingActiveQueue = Queue::whereHas('user', function ($query) use ($data) {
                $query->where('nik', $data->nik);
            })
                ->where('department_id', $data->departmentId)
                ->whereDate('booking_date', $today)
                ->whereIn('status', [QueueStatus::CheckedIn->value, QueueStatus::Serving->value])
                ->exists();

            if ($existingActiveQueue) {
                throw ValidationException::withMessages([
                    'nik' => 'Pengunjung dengan NIK ini sudah memiliki antrean aktif hari ini untuk instansi yang sama.',
                ]);
            }
        }

        return DB::transaction(function () use ($data, $today): Queue {
            $department = Department::findOrFail($data->departmentId);

            // 2. Dapatkan atau buat pengguna (pengunjung) walk-in
            $user = null;
            if ($data->nik) {
                $user = User::where('nik', $data->nik)->first();
            }

            if (! $user) {
                $user = User::create([
                    'name' => $data->name,
                    'nik' => $data->nik,
                    'no_telp' => $data->phone,
                    'email' => $data->nik
                        ? $data->nik.'@mpp.sawahlunto.go.id'
                        : 'walkin-'.time().'-'.rand(1000, 9999).'@mpp.sawahlunto.go.id',
                    'password' => Hash::make('password'),
                    'role' => UserRole::Pengunjung,
                    'email_verified_at' => now(),
                    'is_priority' => $data->isPriority,
                ]);
            } else {
                // Perbarui profil dengan nama/telepon terbaru
                $user->update([
                    'name' => $data->name,
                    'no_telp' => $data->phone,
                    'is_priority' => $data->isPriority,
                ]);
            }

            // 3. Dapatkan nomor urut tertinggi hari ini dengan lockForUpdate untuk mencegah race condition
            $queueNumbers = Queue::where('department_id', $department->id)
                ->whereDate('booking_date', $today)
                ->where('is_priority', $data->isPriority)
                ->whereNotNull('queue_number')
                ->lockForUpdate()
                ->pluck('queue_number')
                ->map(function ($num) {
                    $parts = explode('-', $num);

                    return (int) end($parts);
                });

            $nextNumber = $queueNumbers->isEmpty() ? 1 : $queueNumbers->max() + 1;
            $prefix = $data->isPriority ? 'P' : ($department->inisial ?: 'Q');

            // Format: [INISIAL]-[001], contoh: DDK-001
            $queueNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

            // 4. Generate structured booking code
            $dateStr = now()->format('Ymd');
            $bookingCode = 'WI-'.$prefix.'-'.$dateStr.'-'.strtoupper(Str::random(6));

            // 5. Simpan tiket antrean ke tabel queues dengan status Checked-In
            $queue = Queue::create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'next_department_ids' => $data->nextDepartmentIds,
                'booking_code' => $bookingCode,
                'purpose' => $data->purpose,
                'session_name' => 'Walk-In',
                'booking_date' => $today,
                'queue_number' => $queueNumber,
                'sequence_order' => 1,
                'status' => QueueStatus::CheckedIn->value,
                'is_priority' => $data->isPriority,
                'checked_in_at' => now(),
            ]);

            // 6. Broadcast event QueueCreated ke websocket
            event(new QueueCreated($queue));

            // 7. Record activity log
            ActivityLog::record(
                action: 'WALKIN_TICKET',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Admin FO mencetak tiket mandiri Walk-In ({$queueNumber}) tujuan {$department->name} untuk {$user->name}.",
                actorUserId: auth()->id()
            );

            return $queue->load(['user', 'department']);
        });
    }
}
