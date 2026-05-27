<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'service_id', 'booking_code', 'status', 'booking_date'])]
class Booking extends Model
{
    /**
     * Get the customer that made this booking.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service requested in this booking.
     *
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the queue ticket triggered by this booking.
     *
     * @return HasOne<Queue, $this>
     */
    public function queue(): HasOne
    {
        return $this->hasOne(Queue::class);
    }
}
