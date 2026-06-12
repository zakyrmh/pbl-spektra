<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

final class NotificationPolicy
{
    /**
     * Determine whether the user can view/show the notification.
     */
    public function view(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id;
    }
}
