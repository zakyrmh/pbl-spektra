<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'nik', 'phone', 'purpose'])]
class Visitor extends Model
{
    /**
     * Get the queues for this visitor.
     *
     * @return HasMany<Queue, $this>
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }
}
