<?php

declare(strict_types=1);

namespace App\Data\SuperAdmin;

use Illuminate\Support\Collection;

class SuperAdminDashboardData
{
    public function __construct(
        public int $todayKunjunganCount,
        public array $kunjunganPercentage,
        public int $menungguFoCount,
        public array $foStatus,
        public ?float $avgFoCheckInTime,
        public int $waitingCount,
        public int $servingCount,
        public int $totalAntreanGerai,
        public int $totalGerai,
        public int $activeGerai,
        public int $geraiPercentage,
        public Collection $liveDepartments,
        public Collection $liveLogs,
        public array $chartTrenData,
        public array $chartTopGeraiData
    ) {}

    public function toArray(): array
    {
        return [
            'todayKunjunganCount' => $this->todayKunjunganCount,
            'kunjunganPercentage' => $this->kunjunganPercentage,
            'menungguFoCount' => $this->menungguFoCount,
            'foStatus' => $this->foStatus,
            'avgFoCheckInTime' => $this->avgFoCheckInTime,
            'waitingCount' => $this->waitingCount,
            'servingCount' => $this->servingCount,
            'totalAntreanGerai' => $this->totalAntreanGerai,
            'totalGerai' => $this->totalGerai,
            'activeGerai' => $this->activeGerai,
            'geraiPercentage' => $this->geraiPercentage,
            'liveDepartments' => $this->liveDepartments,
            'liveLogs' => $this->liveLogs,
            'chartTrenData' => $this->chartTrenData,
            'chartTopGeraiData' => $this->chartTopGeraiData,
        ];
    }
}
