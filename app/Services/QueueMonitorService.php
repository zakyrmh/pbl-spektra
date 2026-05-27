<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Department;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class QueueMonitorService
{
    /**
     * Get all monitor data including global metrics and department density status.
     *
     * @return array{
     *     metrics: array{
     *         total_waiting: int,
     *         total_serving: int,
     *         average_wait_time: int
     *     },
     *     departments: Collection<int, Department>
     * }
     */
    public function getMonitorData(): array
    {
        $today = Carbon::today();

        // Fetch departments (instansi) with their queues today (eager loading to prevent N+1 queries)
        $departments = Department::with(['queues' => function ($query) use ($today) {
            $query->whereDate('queue_date', $today);
        }])->get();

        // Aggregate metrics in PHP to be database-agnostic (MySQL & SQLite) and limit DB hits to 2 queries
        $allQueues = $departments->flatMap(fn (Department $d) => $d->queues);

        $totalWaiting = $allQueues->filter(fn (Queue $q) => $q->status === 'Waiting')->count();
        $totalServing = $allQueues->filter(fn (Queue $q) => $q->status === 'Serving')->count();

        $calledQueues = $allQueues->filter(fn (Queue $q) => $q->called_at !== null);
        $totalWaitTimeSeconds = 0;
        $calledCount = 0;

        foreach ($calledQueues as $queue) {
            $createdAt = Carbon::parse($queue->created_at);
            $calledAt = Carbon::parse($queue->called_at);

            if ($calledAt->greaterThanOrEqualTo($createdAt)) {
                $totalWaitTimeSeconds += $calledAt->diffInSeconds($createdAt);
                $calledCount++;
            }
        }

        $averageWaitTimeMinutes = $calledCount > 0 ? (int) round(($totalWaitTimeSeconds / $calledCount) / 60) : 0;

        return [
            'metrics' => [
                'total_waiting' => $totalWaiting,
                'total_serving' => $totalServing,
                'average_wait_time' => $averageWaitTimeMinutes,
            ],
            'departments' => $departments,
        ];
    }
}
