<?php

declare(strict_types=1);

namespace App\Broadcasting;

use App\Models\User;

class QueueTrackerChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Otorisasi akses pengguna ke channel ini.
     * Karena channel utama 'queue-tracker' bersifat publik (tidak memerlukan otorisasi),
     * channel kelas ini disiapkan sebagai template/contoh otorisasi jika di masa depan
     * dibutuhkan channel privat khusus untuk operator/admin tertentu.
     */
    public function join(User $user): array|bool
    {
        // Contoh: Hanya izinkan user yang terautentikasi (admin_fo, admin_gerai, super_admin)
        return in_array($user->role->value ?? $user->role, ['admin_fo', 'admin_gerai', 'super_admin'], true);
    }
}
