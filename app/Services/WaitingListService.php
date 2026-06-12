<?php

declare(strict_types=1);

namespace App\Services;

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
