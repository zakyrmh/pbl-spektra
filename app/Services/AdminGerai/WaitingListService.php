<?php

declare(strict_types=1);

namespace App\Services\AdminGerai;

use App\Data\AdminGerai\WaitingListDashboardData;
use App\Enums\QueueStatus;
use App\Events\QueueCreated;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaitingListService
{
    /**
     * Get waiting list dashboard data aggregated by categories (Booked, CheckedIn, Cancelled/Skipped).
     */
    public function getWaitingListDashboardData(Department $department, ?string $search = null): array
    {
        $today = Carbon::today();

        $pendingModels = $this->getQueues($department, $today, [QueueStatus::Booked->value], $search);
        $pendingBookings = $pendingModels->map(fn ($q) => WaitingListDashboardData::fromModel($q));

        $checkedInModels = $this->getQueues($department, $today, [QueueStatus::CheckedIn->value], $search);
        $checkedInBookings = $checkedInModels->map(fn ($q) => WaitingListDashboardData::fromModel($q));

        $cancelledModels = $this->getQueues($department, $today, [QueueStatus::Cancelled->value, QueueStatus::Skipped->value], $search);
        $cancelledBookings = $cancelledModels->map(fn ($q) => WaitingListDashboardData::fromModel($q));

        return [
            'pendingBookings' => $pendingBookings,
            'checkedInBookings' => $checkedInBookings,
            'cancelledBookings' => $cancelledBookings,
        ];
    }

    /**
     * Get queues for the department filtered by status and optional search term.
     *
     * @param  array<string>  $statuses
     * @return Collection<int, Queue>
     */
    public function getQueues(Department $dept, Carbon $date, array $statuses, ?string $search = null): Collection
    {
        $query = Queue::where('department_id', $dept->id)
            ->whereDate('booking_date', $date)
            ->whereIn('status', $statuses)
            ->with('user');

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->get();
    }

    /**
     * Verify/Check-in a booking.
     */
    public function checkIn(Queue $queue): void
    {
        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => QueueStatus::CheckedIn->value,
                'checked_in_at' => now(),
            ]);

            ActivityLog::record(
                action: 'VERIFY_CHECKIN',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator Gerai memproses check-in antrean {$queue->booking_code}.",
                actorUserId: Auth::id(),
            );

            // Dispatch broadcast event
            event(new QueueCreated($queue));
        });
    }

    /**
     * Cancel booking.
     */
    public function cancel(Queue $queue, string $reason): void
    {
        DB::transaction(function () use ($queue, $reason) {

            $queue->update([
                'status' => QueueStatus::Cancelled->value,
                'cancel_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            ActivityLog::record(
                action: 'CANCEL_BOOKING',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator Gerai membatalkan booking {$queue->booking_code}. Alasan: {$reason}",
                actorUserId: Auth::id(),
            );

            // Dispatch broadcast event
            event(new QueueCreated($queue));
        });
    }

    /**
     * Restore cancelled queue status.
     */
    public function restore(Queue $queue): void
    {
        DB::transaction(function () use ($queue) {
            // Restore status: if checked_in_at exists, restore to Checked-In, otherwise restore to Booked
            $newStatus = $queue->checked_in_at ? QueueStatus::CheckedIn->value : QueueStatus::Booked->value;

            $queue->update([
                'status' => $newStatus,
                'cancel_reason' => null,
            ]);

            ActivityLog::record(
                action: 'RESTORE_BOOKING',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator Gerai memulihkan status antrean {$queue->booking_code} kembali ke {$newStatus}.",
                actorUserId: Auth::id(),
            );
        });
    }
}
