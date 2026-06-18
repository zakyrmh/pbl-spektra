<?php

declare(strict_types=1);

namespace App\Data\AdminGerai;

use App\Models\Department;
use App\Models\Queue;
use Illuminate\Support\Collection;

final class BoothDashboardData
{
    /**
     * @param  Collection<int, Queue>  $waitingQueues
     * @param  Collection<int, Queue>  $skippedQueues
     * @param  Collection<int, Department>  $activeDepartments
     */
    public function __construct(
        public Department $department,
        public ?Queue $activeQueue,
        public Collection $waitingQueues,
        public Collection $skippedQueues,
        public int $remainingCount,
        public int $avgServiceTime,
        public Collection $activeDepartments
    ) {}
}
