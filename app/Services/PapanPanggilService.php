<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\QueueStatus;
use App\Events\QueueCalled;
use App\Events\QueueFinished;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PapanPanggilService
{
    /**
     * Get the current serving queue for the department.
     */
    public function getCurrentQueue(Department $dept, Carbon $date): ?Queue
    {
        return Queue::where('department_id', $dept->id)
            ->whereDate('booking_date', $date)
            ->where('status', QueueStatus::Serving->value)
            ->first();
    }

    /**
     * Get the next waiting queue in line.
     */
    public function getNextQueue(Department $dept, Carbon $date): ?Queue
    {
        return Queue::where('department_id', $dept->id)
            ->whereDate('booking_date', $date)
            ->where('status', QueueStatus::CheckedIn->value)
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Get all remaining checked-in queues.
     *
     * @return Collection<int, Queue>
     */
    public function getRemainingQueues(Department $dept, Carbon $date): Collection
    {
        return Queue::where('department_id', $dept->id)
            ->whereDate('booking_date', $date)
            ->where('status', QueueStatus::CheckedIn->value)
            ->orderBy('id', 'asc')
            ->with('user')
            ->get();
    }

    /**
     * Call the next ticket.
     */
    public function callNext(Department $dept, Carbon $date): ?Queue
    {
        $nextQueue = null;

        DB::transaction(function () use ($dept, $date, &$nextQueue) {
            // Selesaikan antrean yang sedang dilayani saat ini (Serving -> Completed)
            $currentQueue = $this->getCurrentQueue($dept, $date);
            if ($currentQueue) {
                $currentQueue->update([
                    'status' => QueueStatus::Completed->value,
                    'completed_at' => now(),
                ]);

                ActivityLog::record(
                    action: 'COMPLETE_GERAI',
                    modelType: 'Queue',
                    modelId: $currentQueue->id,
                    description: "Menyelesaikan pelayanan antrean gerai: {$currentQueue->booking_code}",
                    actorUserId: Auth::id()
                );

                event(new QueueFinished($currentQueue));
            }

            // Panggil antrean berikutnya (CheckedIn -> Serving)
            $nextQueue = Queue::where('department_id', $dept->id)
                ->whereDate('booking_date', $date)
                ->where('status', QueueStatus::CheckedIn->value)
                ->orderBy('id', 'asc')
                ->first();

            if ($nextQueue) {
                $nextQueue->update([
                    'status' => QueueStatus::Serving->value,
                    'called_at' => now(),
                ]);

                ActivityLog::record(
                    action: 'CALL_NEXT_GERAI',
                    modelType: 'Queue',
                    modelId: $nextQueue->id,
                    description: "Memanggil antrean gerai berikutnya: {$nextQueue->booking_code}",
                    actorUserId: Auth::id()
                );

                event(new QueueCalled($nextQueue));
            }
        });

        return $nextQueue;
    }

    /**
     * Complete the active queue.
     */
    public function complete(Queue $queue): void
    {
        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => QueueStatus::Completed->value,
                'completed_at' => now(),
            ]);

            ActivityLog::record(
                action: 'COMPLETE_GERAI',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Menyelesaikan pelayanan antrean gerai: {$queue->booking_code}",
                actorUserId: Auth::id()
            );

            event(new QueueFinished($queue));
        });
    }

    /**
     * Skip/cancel the active queue.
     */
    public function skip(Queue $queue, string $reason): void
    {
        DB::transaction(function () use ($queue, $reason) {
            $queue->update([
                'status' => QueueStatus::Cancelled->value,
                'cancel_reason' => $reason,
                'completed_at' => now(),
            ]);

            ActivityLog::record(
                action: 'SKIP_GERAI',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Melewati antrean gerai: {$queue->booking_code}. Alasan: {$reason}",
                actorUserId: Auth::id()
            );

            event(new QueueFinished($queue));
        });
    }
}
