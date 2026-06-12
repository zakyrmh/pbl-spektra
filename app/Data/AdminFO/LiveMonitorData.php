<?php

declare(strict_types=1);

namespace App\Data\AdminFO;

use Illuminate\Support\Collection;

class LiveMonitorData
{
    /**
     * @param  Collection<int, DepartmentMonitorData>  $departments
     */
    public function __construct(
        public int $totalWaiting,
        public int $totalServing,
        public int $averageWaitTime,
        public Collection $departments
    ) {}

    /**
     * Convert the DTO to a representation suitable for views or simple arrays.
     *
     * @return array{
     *     metrics: array{
     *         total_waiting: int,
     *         total_serving: int,
     *         average_wait_time: int
     *     },
     *     departments: Collection<int, DepartmentMonitorData>
     * }
     */
    public function toViewArray(): array
    {
        return [
            'metrics' => [
                'total_waiting' => $this->totalWaiting,
                'total_serving' => $this->totalServing,
                'average_wait_time' => $this->averageWaitTime,
            ],
            'departments' => $this->departments,
        ];
    }
}
