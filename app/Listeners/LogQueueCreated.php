<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\QueueCreated;
use Illuminate\Support\Facades\Log;

final class LogQueueCreated
{
    /**
     * Handle the event.
     */
    public function handle(QueueCreated $event): void
    {
        Log::info("Queue entry created: Number '{$event->queueEntry->queue_number}' for user ID '{$event->queueEntry->user_id}'.");
    }
}
