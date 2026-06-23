<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'department_id',
    'booking_code',
    'purpose',
    'session_name',
    'booking_date',
    'queue_number',
    'status',
    'cancel_reason',
    'checked_in_at',
    'called_at',
    'cancelled_at',
    'completed_at',
])]
class Queue extends Model
{
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'checked_in_at' => 'datetime',
            'called_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
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
     * Get the user who owns this queue.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department associated with this queue.
     *
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope query untuk mengecualikan antrean yang dibatalkan oleh FO.
     * Hanya berlaku untuk role Gerai.
     */
    public function scopeExcludeCancelled($query)
    {
        return $query->where('status', '!=', 'Cancelled');
    }
}
