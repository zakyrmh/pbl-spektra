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
        public int $estimatedTime
    ) {}

    public function toArray(): array
    {
        return [
            'activeBooking' => $this->activeBooking,
            'currentServingQueue' => $this->currentServingQueue,
            'remainingQueuesCount' => $this->remainingQueuesCount,
            'estimatedTime' => $this->estimatedTime,
        ];
    }
}
