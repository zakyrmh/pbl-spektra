<?php

declare(strict_types=1);

namespace App\Services\Public;

use App\Models\Notification;
use App\Models\Queue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class NotificationService
{
    /**
     * Get paginated user notifications.
     */
    public function getUserNotifications(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        /** @var Builder $query */
        $query = Notification::query();

        return $query->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Mark notification as read and look for a completed queue without feedback.
     */
    public function markAsReadAndFindUnreviewedQueue(Notification $notification, int $userId): ?Queue
    {
        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        /** @var Builder $query */
        $query = Queue::query();

        return $query->where('user_id', $userId)
            ->where('status', 'Completed')
            ->whereDoesntHave('feedback')
            ->orderBy('completed_at', 'desc')
            ->first();
    }
}
