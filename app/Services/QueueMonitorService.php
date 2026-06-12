<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\DepartmentMonitorData;
use App\Data\LiveMonitorData;
use App\Enums\QueueStatus;
use App\Models\Department;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class QueueMonitorService
{
    /**
     * Get all monitor data including global metrics and department density status.
     */
    public function getMonitorData(): LiveMonitorData
    {
        $today = Carbon::today();

        // Fetch departments (instansi) with their queues today (eager loading to prevent N+1 queries)
        $departments = Department::with(['queues' => function ($query) use ($today) {
            $query->whereDate('booking_date', $today);
        }])->get();

        // Aggregate metrics in PHP to be database-agnostic
        $allQueues = $departments->flatMap(fn (Department $d) => $d->queues);

        $totalWaiting = $allQueues->filter(function (Queue $q) {
            return $q->status === QueueStatus::CheckedIn->value || $q->status === QueueStatus::CheckedIn;
        })->count();

        $totalServing = $allQueues->filter(function (Queue $q) {
            return $q->status === QueueStatus::Serving->value || $q->status === QueueStatus::Serving;
        })->count();

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

        // Map departments to DepartmentMonitorData DTOs
        $departmentsData = $departments->map(function (Department $dept) {
            $waiting = $dept->queues->filter(function (Queue $q) {
                return $q->status === QueueStatus::CheckedIn->value || $q->status === QueueStatus::CheckedIn;
            })->count();

            $serving = $dept->queues->filter(function (Queue $q) {
                return $q->status === QueueStatus::Serving->value || $q->status === QueueStatus::Serving;
            })->count();

            return DepartmentMonitorData::fromModel($dept, $waiting, $serving);
        });

        return new LiveMonitorData(
            totalWaiting: $totalWaiting,
            totalServing: $totalServing,
            averageWaitTime: $averageWaitTimeMinutes,
            departments: $departmentsData
        );
    }

    /**
     * Get all departments with their current serving queue for the public display.
     *
     * @return Collection<int, Department>
     */
    public function getPublicDisplayDepartments(): Collection
    {
        $today = Carbon::today();

        return Department::with(['queues' => function ($query) use ($today) {
            $query->whereDate('booking_date', $today)
                ->where('status', QueueStatus::Serving->value);
        }])->get();
    }
}
