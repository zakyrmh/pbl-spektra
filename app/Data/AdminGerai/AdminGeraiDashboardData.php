<?php

declare(strict_types=1);

namespace App\Data\AdminGerai;

use App\Models\Department;
use Illuminate\Support\Collection;

final readonly class AdminGeraiDashboardData
{
    public function __construct(
        public ?Department $department,
        public mixed $currentQueue,
        public mixed $activeQueue,
        public Collection $waitingQueues,
        public Collection $skippedQueues,
        public int $completedCount,
        public int $remainingCount,
        public int $avgServiceTime,
        public bool $noCounter
    ) {}

    public function toArray(): array
    {
        return [
            'department' => $this->department,
            'currentQueue' => $this->currentQueue,
            'activeQueue' => $this->activeQueue,
            'waitingQueues' => $this->waitingQueues,
            'skippedQueues' => $this->skippedQueues,
            'completedCount' => $this->completedCount,
            'remainingCount' => $this->remainingCount,
            'avgServiceTime' => $this->avgServiceTime,
            'noCounter' => $this->noCounter,
        ];
    }
}
