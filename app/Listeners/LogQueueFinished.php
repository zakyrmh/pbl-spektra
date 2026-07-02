<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\QueueFinished;
use Illuminate\Support\Facades\Log;

final class LogQueueFinished
{
    /**
     * Handle the event.
     */
    public function handle(QueueFinished $event): void
    {
        $statusValue = $event->queueEntry->status instanceof \BackedEnum ? $event->queueEntry->status->value : $event->queueEntry->status;
        Log::info("Queue entry finished: Number '{$event->queueEntry->queue_number}' status '{$statusValue}'.");
    }
}
