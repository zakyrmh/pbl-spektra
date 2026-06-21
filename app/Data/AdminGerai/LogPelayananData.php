<?php

declare(strict_types=1);

namespace App\Data\AdminGerai;

use App\Models\Queue;
use Carbon\Carbon;

/**
 * Immutable data contract for a single log pelayanan (completed/skipped/cancelled) row.
 */
final class LogPelayananData
{
    public function __construct(
        public readonly int $id,
        public readonly string $queue_number,
        public readonly string $booking_code,
        public readonly ?string $visitor_name,
        public readonly ?string $purpose,
        public readonly ?string $called_at_formatted,
        public readonly ?string $completed_at_formatted,
        public readonly ?string $duration_label,
        public readonly string $status,
        public readonly ?string $cancel_reason,
        public readonly ?string $booking_date_formatted,
    ) {}

    /**
     * Build a LogPelayananData from a Queue Eloquent model.
     */
    public static function fromModel(Queue $queue): self
    {
        $durationLabel = null;
        if ($queue->called_at && $queue->completed_at) {
            $seconds = (int) abs($queue->completed_at->diffInSeconds($queue->called_at));
            if ($seconds < 60) {
                $durationLabel = "{$seconds} dtk";
            } else {
                $minutes = (int) floor($seconds / 60);
                $remaining = $seconds % 60;
                $durationLabel = $remaining > 0
                    ? "{$minutes} mnt {$remaining} dtk"
                    : "{$minutes} mnt";
            }
        }

        return new self(
            id: $queue->id,
            queue_number: $queue->queue_number ?? '-',
            booking_code: $queue->booking_code,
            visitor_name: $queue->user?->name,
            purpose: $queue->purpose,
            called_at_formatted: $queue->called_at?->format('H:i:s'),
            completed_at_formatted: $queue->completed_at?->format('H:i:s'),
            duration_label: $durationLabel,
            status: $queue->status->value ?? $queue->status,
            cancel_reason: $queue->cancel_reason,
            booking_date_formatted: $queue->booking_date
                ? Carbon::parse($queue->booking_date)->format('d M Y')
                : '-',
        );
    }
}
