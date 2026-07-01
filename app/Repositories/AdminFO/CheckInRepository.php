<?php

declare(strict_types=1);

namespace App\Repositories\AdminFO;

use App\Models\Queue;
use Illuminate\Support\Collection;

class CheckInRepository
{
    /**
     * Find queue by booking code or NIK.
     */
    public function findByBookingCode(string $code): ?Queue
    {
        $code = trim($code);

        // NIK is 16 digits
        if (preg_match('/^\d{16}$/', $code)) {
            return Queue::whereHas('user', function ($query) use ($code) {
                $query->where('nik', $code);
            })
                ->with(['user', 'department'])
                ->first();
        }

        return Queue::where('booking_code', $code)
            ->with(['user', 'department'])
            ->first();
    }

    /**
     * Find queue by ID.
     */
    public function findById(int $id): ?Queue
    {
        return Queue::with(['user', 'department'])->find($id);
    }

    /**
     * Get active queue count for a department on a specific date.
     */
    public function getActiveCountByDepartment(int $departmentId, string $date): int
    {
        return Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $date)
            ->whereNotNull('queue_number')
            ->count();
    }

    /**
     * Check if a citizen NIK has an active queue today.
     */
    public function hasActiveQueueToday(string $nik, int $departmentId, string $date, int $excludeId): bool
    {
        return Queue::whereHas('user', function ($query) use ($nik) {
            $query->where('nik', $nik);
        })
            ->where('department_id', $departmentId)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['Checked-In', 'Serving'])
            ->where('id', '!=', $excludeId)
            ->exists();
    }

    /**
     * Get queue numbers for a department today to generate the next number.
     */
    public function getTodayQueueNumbers(int $departmentId, string $date): Collection
    {
        return Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $date)
            ->whereNotNull('queue_number')
            ->pluck('queue_number');
    }

    /**
     * Save/update the queue model.
     */
    public function save(Queue $queue): Queue
    {
        $queue->save();

        return $queue;
    }
}
