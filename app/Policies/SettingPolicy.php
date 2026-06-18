<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class SettingPolicy
{
    /**
     * Determine whether the user can manage settings.
     */
    public function manage(User $actor): bool
    {
        return $actor->role === UserRole::SuperAdmin;
    }
}
