<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QueueStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'parent_queue_id',
    'department_id',
    'next_department_ids',
    'booking_code',
    'purpose',
    'session_name',
    'booking_date',
    'queue_number',
    'sequence_order',
    'status',
    'is_priority',
    'is_waterfall_forwarded',
    'cancel_reason',
    'visit_notes',
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
            'status' => QueueStatus::class,
            'checked_in_at' => 'datetime',
            'called_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_priority' => 'boolean',
            'is_waterfall_forwarded' => 'boolean',
            'next_department_ids' => 'array',
            'sequence_order' => 'integer',
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
     * Get the parent queue (initial step) of this waterfall visit.
     *
     * @return BelongsTo<Queue, $this>
     */
    public function parentQueue(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_queue_id');
    }

    /**
     * Get the child queues (subsequent steps) of this waterfall visit.
     */
    public function childQueues()
    {
        return $this->hasMany(self::class, 'parent_queue_id');
    }

    /**
     * Get all queue steps related to this visit.
     */
    public function allVisitQueues()
    {
        $rootId = $this->parent_queue_id ?? $this->id;

        return self::where('id', $rootId)->orWhere('parent_queue_id', $rootId)->orderBy('sequence_order', 'asc');
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
        return $query->where('status', '!=', QueueStatus::Cancelled);
    }
}
