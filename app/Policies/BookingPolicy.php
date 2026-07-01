<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Queue;
use App\Models\User;

final class BookingPolicy
{
    /**
     * Determine whether the user can view the booking/queue ticket.
     */
    public function view(User $user, Queue $booking): bool
    {
        // Only the ticket owner (user_id match), an admin_fo, or a super_admin can view the digital ticket data context.
        return $booking->user_id === $user->id
            || $user->hasRole(UserRole::AdminFo)
            || $user->hasRole(UserRole::SuperAdmin);
    }
}
