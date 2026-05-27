<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['department_id', 'name', 'location'])]
class Counter extends Model
{
    /**
     * Get the department that owns this counter.
     *
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the queues for this counter.
     *
     * @return HasMany<Queue, $this>
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }
}
