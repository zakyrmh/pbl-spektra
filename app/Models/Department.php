<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'inisial', 'description'])]
class Department extends Model
{
    /**
     * Get the services offered by this department.
     *
     * @return HasMany<Service, $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the counters (loket) associated with this department.
     *
     * @return HasMany<Counter, $this>
     */
    public function counters(): HasMany
    {
        return $this->hasMany(Counter::class);
    }

    /**
     * Get the queues for this department through counters.
     *
     * @return HasManyThrough<Queue, Counter, $this>
     */
    public function queues(): HasManyThrough
    {
        return $this->hasManyThrough(Queue::class, Counter::class);
    }
}
