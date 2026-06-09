<?php

declare(strict_types=1);

namespace App\Data;

/**
 * Data Transfer Object untuk statistik landing page publik.
 *
 * Immutable & readonly — begitu dibuat, isinya tidak bisa berubah.
 * Ini menjamin type safety dari Service sampai ke View.
 */
final readonly class LandingStats
{
    public function __construct(
        public int $totalInstansi,
        public string $rataWaktuTunggu,
    ) {}
}
