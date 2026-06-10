<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'booking_id',
    'visitor_id',
    'counter_id',
    'service_id',
    'queue_number',
    'status',
    'called_at',
    'completed_at',
    'queue_date',
])]
class Queue extends Model
{
    protected function casts(): array
    {
        return [
            'called_at' => 'datetime',
            'completed_at' => 'datetime',
            'queue_date' => 'date',
        ];
    }

    /**
     * Calculate service duration in seconds.
     */
    public function calculateDuration(): ?int
    {
        if ($this->called_at && $this->completed_at) {
            return (int) abs($this->completed_at->diffInSeconds($this->called_at));
        }

        return null;
    }

    /**
     * Get the feedback associated with this queue.
     */
    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }

    /**
     * Get the booking that triggered this queue.
     *
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the walk-in visitor that owns this queue ticket.
     *
     * @return BelongsTo<Visitor, $this>
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Get the counter (loket) processing this queue.
     *
     * @return BelongsTo<Counter, $this>
     */
    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    /**
     * Get the service for this queue.
     *
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
