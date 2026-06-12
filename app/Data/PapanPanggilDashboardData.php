<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Queue;
use Carbon\Carbon;

class PapanPanggilDashboardData
{
    public function __construct(
        public int $id,
        public string $booking_code,
        public string $status,
        public ?string $purpose,
        public ?Carbon $checked_in_at,
        public ?object $user
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
            checked_in_at: $queue->checked_in_at ? Carbon::parse($queue->checked_in_at) : null,
            user: $userObj
        );
    }
}
