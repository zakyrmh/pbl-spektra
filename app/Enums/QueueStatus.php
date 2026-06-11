<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Status antrean. Pakai PHP 8.1+ backed enum supaya:
 *  - Nilai string di-DB tetap konsisten (no typo)
 *  - IDE & static analysis bisa autocomplete
 *  - Mudah ditambah method (label(), color(), dst.) untuk UI
 */
enum QueueStatus: string
{
    case Booked = 'Booked';
    case CheckedIn = 'Checked-In';
    case Serving = 'Serving';
    case Completed = 'Completed';
    case Skipped = 'Skipped';
    case Cancelled = 'Cancelled';

    /**
     * Status yang dianggap "sudah selesai" untuk statistik.
     *
     * @return list<self>
     */
    public static function finished(): array
    {
        return [self::Completed, self::Skipped, self::Cancelled];
    }

    public function isFinished(): bool
    {
        return in_array($this, self::finished(), true);
    }
}
