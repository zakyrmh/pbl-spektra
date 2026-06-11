<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Status operasional booth/gerai.
 */
enum BoothStatus: string
{
    case Buka = 'buka';
    case Istirahat = 'istirahat';
    case Tutup = 'tutup';

    /**
     * Dapatkan semua nilai enum.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Dapatkan label representasi human-readable.
     */
    public function label(): string
    {
        return match ($this) {
            self::Buka => 'Buka',
            self::Istirahat => 'Istirahat',
            self::Tutup => 'Tutup',
        };
    }
}
