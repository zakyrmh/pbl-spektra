<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\LandingStats;
use App\Enums\QueueStatus;
use App\Models\Department as Instansi;
use App\Models\Queue;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class PublicDashboardService
{
    /**
     * Cache key & TTL untuk statistik landing page.
     * Dipakai ulang antara request untuk mengurangi beban DB.
     */
    private const CACHE_TTL_SECONDS = 300;        // 5 menit

    private const CACHE_KEY_INSTANSI = 'public:stats:total_instansi';

    private const CACHE_KEY_WAIT_TIME = 'public:stats:avg_waiting_time';

    /**
     * Konstanta fallback ketika DB belum siap / terjadi error.
     */
    private const DEFAULT_INSTANSI_COUNT = 0;

    private const DEFAULT_WAITING_TIME = '0';

    private const FALLBACK_WAITING_TIME = '15 Menit';

    /**
     * Batas sample antrean yang dihitung (100 terakhir yang selesai).
     */
    private const QUEUE_SAMPLE_SIZE = 100;

    /**
     * Ambil statistik untuk halaman landing publik.
     *
     * Method ini didesain untuk TIDAK pernah throw exception
     * ke caller — semua error dicatat dan di-fallback ke default
     * supaya halaman publik tetap bisa dirender.
     */
    public function getLandingStats(): LandingStats
    {
        try {
            return new LandingStats(
                totalInstansi: $this->getTotalActiveInstansi(),
                rataWaktuTunggu: $this->calculateAverageWaitingTime(),
            );
        } catch (Throwable $e) {
            report($e); // lempar ke logger (Sentry, Log, dll.) tanpa ganggu user

            return new LandingStats(
                totalInstansi: self::DEFAULT_INSTANSI_COUNT,
                rataWaktuTunggu: self::DEFAULT_WAITING_TIME,
            );
        }
    }

    private function getTotalActiveInstansi(): int
    {
        return Cache::remember(
            self::CACHE_KEY_INSTANSI,
            self::CACHE_TTL_SECONDS,
            fn (): int => $this->fetchTotalActiveInstansi(),
        );
    }

    private function fetchTotalActiveInstansi(): int
    {
        if (! Schema::hasTable('departments')) {
            return self::DEFAULT_INSTANSI_COUNT;
        }

        $query = Instansi::query();

        if (Schema::hasColumn('departments', 'is_active')) {
            $query->where('is_active', true);
        }

        return $query->count();
    }

    private function calculateAverageWaitingTime(): string
    {
        return Cache::remember(
            self::CACHE_KEY_WAIT_TIME,
            self::CACHE_TTL_SECONDS,
            fn (): string => $this->fetchAverageWaitingTime(),
        );
    }

    private function fetchAverageWaitingTime(): string
    {
        if (! Schema::hasTable('queues')) {
            return self::DEFAULT_WAITING_TIME;
        }

        $completedQueues = Queue::query()
            ->where('status', QueueStatus::Completed->value)
            ->whereNotNull('called_at')
            ->whereNotNull('created_at')
            ->latest('called_at')
            ->limit(self::QUEUE_SAMPLE_SIZE)
            ->get(['created_at', 'called_at']);

        if ($completedQueues->isEmpty()) {
            return self::DEFAULT_WAITING_TIME;
        }

        $totalMinutes = 0;
        $validSamples = 0;

        foreach ($completedQueues as $queue) {
            $created = CarbonImmutable::parse($queue->created_at);
            $called = CarbonImmutable::parse($queue->called_at);

            // Abaikan data anomali (waktu pemanggilan < waktu dibuat)
            if ($called->lessThan($created)) {
                continue;
            }

            $totalMinutes += (int) abs($created->diffInMinutes($called));
            $validSamples++;
        }

        if ($validSamples === 0) {
            return self::FALLBACK_WAITING_TIME;
        }

        return round($totalMinutes / $validSamples).' Menit';
    }
}
