<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'schedule_id',
        'purpose',
        'booking_code',
        'status',
        'cancel_reason',
        'booking_date',
        'checked_in_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'checked_in_at' => 'datetime',
    ];

    /**
     * Get the schedule for this booking.
     *
     * @return BelongsTo<Schedule, $this>
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

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

    /**
     * Scope a query to only include bookings belonging to a specific department.
     *
     * @param  Builder  $query
     * @param  int|null  $departmentId
     * @return Builder
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->whereHas('service', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });
    }

    /**
     * Apakah booking ini bisa di-check-in?
     * Hanya booking berstatus 'Pending' yang valid.
     */
    public function canBeCheckedIn(): bool
    {
        return $this->status === 'Pending';
    }
}
