<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Queue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Queue $queue;

    /**
     * Create a new event instance.
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('queue-tracker'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'queue.finished';
    }
}
