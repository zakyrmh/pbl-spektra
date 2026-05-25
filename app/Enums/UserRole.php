<?php

namespace App\Enums;

/**
 * Enum peran (role) pengguna dalam sistem MPP Sawahlunto.
 *
 * Backed dengan string agar kompatibel dengan kolom `role` di database.
 * Laravel 13 secara otomatis men-cast nilai DB → Enum dan sebaliknya.
 */
enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case AdminFo = 'admin_fo';
    case AdminGerai = 'admin_gerai';
    case Pengunjung = 'pengunjung';

    /** Label human-readable Bahasa Indonesia. */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::AdminFo => 'Admin Front Office',
            self::AdminGerai => 'Operator Loket',
            self::Pengunjung => 'Pengunjung',
        };
    }

    /**
     * Tailwind CSS class untuk badge warna role.
     *
     * @return string Kelas CSS badge
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::SuperAdmin => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            self::AdminFo => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            self::AdminGerai => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
            self::Pengunjung => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        };
    }

    /**
     * Role yang dianggap sebagai "staf internal" MPP (bukan pengunjung publik).
     *
     * @return array<self>
     */
    public static function staffRoles(): array
    {
        return [self::SuperAdmin, self::AdminFo, self::AdminGerai];
    }

    /**
     * Role yang wajib dipetakan ke instansi dan nomor loket.
     *
     * @return array<self>
     */
    public static function requiresInstansi(): array
    {
        return [self::AdminGerai];
    }

    /**
     * Ambil semua nilai string (untuk Rule::in validation).
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
