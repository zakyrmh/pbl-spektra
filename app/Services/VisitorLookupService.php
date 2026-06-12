<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;

class VisitorLookupService
{
    /**
     * Look up a visitor by NIK.
     */
    public function findByNik(string $nik): ?User
    {
        return User::where('nik', $nik)
            ->where('role', UserRole::Pengunjung)
            ->first();
    }
}
