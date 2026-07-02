<?php

namespace App\Enums;

/**
 * Enum status akun pengguna dalam sistem MPP Sawahlunto.
 */
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    /** Label human-readable Bahasa Indonesia. */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Inactive => 'Nonaktif',
        };
    }

    /** Tailwind CSS class untuk badge. */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
            self::Inactive => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
        };
    }

    /** Dot indicator color class. */
    public function dotClass(): string
    {
        return match ($this) {
            self::Active => 'bg-emerald-500',
            self::Inactive => 'bg-gray-400',
        };
    }

    /** Bangun dari nilai boolean `is_active`. */
    public static function fromBool(bool $isActive): self
    {
        return $isActive ? self::Active : self::Inactive;
    }
}
