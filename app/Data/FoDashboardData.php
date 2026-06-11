<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Support\Collection;

class FoDashboardData
{
    public function __construct(
        public Collection $departments,
        public Collection $recentQueues,
        public int $todayFoQueueCount,
        public int $todayTotalPrintedTickets
    ) {}

    public function toArray(): array
    {
        return [
            'departments' => $this->departments,
            'recentQueues' => $this->recentQueues,
            'todayFoQueueCount' => $this->todayFoQueueCount,
            'todayTotalPrintedTickets' => $this->todayTotalPrintedTickets,
        ];
    }
}
