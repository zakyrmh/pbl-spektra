<?php

declare(strict_types=1);

namespace App\Services\Public;

use App\Data\Public\LandingStats;
use App\Models\Department;
use App\Models\Queue;

final class PublicDashboardService
{
    /**
     * Compile public landing page statistics.
     */
    public function getLandingStats(): LandingStats
    {
        $totalInstansi = Department::query()->where('is_open', true)->count();

        $avgSeconds = null;
        if (config('database.default') === 'sqlite') {
            $avgSeconds = Queue::query()->where('status', 'Completed')
                ->whereNotNull('called_at')
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(strftime("%s", completed_at) - strftime("%s", called_at)) as avg_duration')
                ->value('avg_duration');
        } else {
            $avgSeconds = Queue::query()->where('status', 'Completed')
                ->whereNotNull('called_at')
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, called_at, completed_at)) as avg_duration')
                ->value('avg_duration');
        }

        $rataWaktuTunggu = '0 menit';
        if ($avgSeconds !== null) {
            $avgMinutes = (int) round((float) $avgSeconds / 60);
            $rataWaktuTunggu = $avgMinutes.' menit';
        }

        return new LandingStats(
            totalInstansi: $totalInstansi,
            rataWaktuTunggu: $rataWaktuTunggu
        );
    }
}
