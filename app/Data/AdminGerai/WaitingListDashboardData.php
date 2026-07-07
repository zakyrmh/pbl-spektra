<?php

declare(strict_types=1);

namespace App\Data\AdminGerai;

use App\Enums\QueueStatus;
use App\Models\Queue;
use Carbon\Carbon;

class WaitingListDashboardData
{
    public function __construct(
        public int $id,
        public string $booking_code,
        public QueueStatus $status,
        public ?string $purpose,
        public ?string $session_name,
        public Carbon $booking_date,
        public ?Carbon $checked_in_at,
        public ?Carbon $called_at,
        public ?string $cancel_reason,
        public ?object $user,
        public bool $is_priority = false
    ) {}

    /**
     * Map Queue model to DTO.
     */
    public static function fromModel(Queue $queue): self
    {
        $userObj = null;
        if ($queue->user) {
            $userObj = (object) [
                'id' => $queue->user->id,
                'name' => $queue->user->name,
                'nik' => $queue->user->nik,
            ];
        }

        return new self(
            id: $queue->id,
            booking_code: $queue->booking_code,
            status: $queue->status,
            purpose: $queue->purpose,
            session_name: $queue->session_name,
            booking_date: Carbon::parse($queue->booking_date),
            checked_in_at: $queue->checked_in_at ? Carbon::parse($queue->checked_in_at) : null,
            called_at: $queue->called_at ? Carbon::parse($queue->called_at) : null,
            cancel_reason: $queue->cancel_reason,
            user: $userObj,
            is_priority: (bool) $queue->is_priority
        );
    }
}
