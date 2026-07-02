<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\QueueCalled;
use Illuminate\Support\Facades\Log;

final class LogQueueCalled
{
    /**
     * Handle the event.
     */
    public function handle(QueueCalled $event): void
    {
        Log::info("Queue entry called: Number '{$event->queueNumber}' at '{$event->counterName}' for department '{$event->departmentName}'.");
    }
}
