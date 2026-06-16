<?php

declare(strict_types=1);

namespace App\Data\Public;

use App\Models\Queue;

final readonly class VisitorDashboardData
{
    public function __construct(
        public ?Queue $activeBooking,
        public string $currentServingQueue,
        public int $remainingQueuesCount,
        public int $estimatedTime,
        public int $densityPercentage,
        public string $densityStatus,
        public string $densityClass,
        public string $densityDot,
        public string $densityDescription,
        public array $topDepartments
    ) {}

    public function toArray(): array
    {
        return [
            'activeBooking' => $this->activeBooking,
            'currentServingQueue' => $this->currentServingQueue,
            'remainingQueuesCount' => $this->remainingQueuesCount,
            'estimatedTime' => $this->estimatedTime,
            'densityPercentage' => $this->densityPercentage,
            'densityStatus' => $this->densityStatus,
            'densityClass' => $this->densityClass,
            'densityDot' => $this->densityDot,
            'densityDescription' => $this->densityDescription,
            'topDepartments' => $this->topDepartments,
        ];
    }
}
