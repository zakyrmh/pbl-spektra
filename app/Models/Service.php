<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'description',
    ];

    /**
     * Get the department (gerai) that owns this service.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the counters that serve this service.
     */
    public function counters(): BelongsToMany
    {
        return $this->belongsToMany(Counter::class, 'counter_service');
    }
}
