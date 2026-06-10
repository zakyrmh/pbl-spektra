<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'service_id',
        'date',
        'session_name',
        'quota_total',
        'quota_used',
        'is_open',
    ];

    protected $casts = [
        'date' => 'date',
        'is_open' => 'boolean',
        'quota_total' => 'integer',
        'quota_used' => 'integer',
    ];

    /**
     * Get the service that owns the schedule.
     *
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the bookings registered under this schedule.
     *
     * @return HasMany<Booking>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if schedule quota is full.
     */
    public function isFull(): bool
    {
        return $this->quota_used >= $this->quota_total;
    }
}
