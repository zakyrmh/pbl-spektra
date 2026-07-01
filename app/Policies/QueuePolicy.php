<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Queue;
use App\Models\User;

class QueuePolicy
{
    /**
     * Determine whether the user can view or manage the queue.
     */
    public function manage(User $user, Queue $queue): bool
    {
        // Super Admin has full access
        if ($user->hasRole(UserRole::SuperAdmin)) {
            return true;
        }

        // Must be admin_gerai
        if (! $user->hasRole(UserRole::AdminGerai)) {
            return false;
        }

        // User must have a counter assigned
        if (! $user->counter_id) {
            return false;
        }

        $userCounter = $user->counter;
        $queueCounter = $queue->counter;

        if (! $userCounter || ! $queueCounter) {
            return false;
        }

        // Must be the same department (Gerai)
        return $userCounter->department_id === $queueCounter->department_id;
    }

    /**
     * Determine whether the user can view the queue.
     */
    public function view(User $user, Queue $queue): bool
    {
        return $this->manage($user, $queue);
    }

    /**
     * Determine whether the user can update the queue.
     */
    public function update(User $user, Queue $queue): bool
    {
        return $this->manage($user, $queue);
    }
}
