<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Counter extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'location',
        'status',
    ];

    /**
     * Get the department (gerai) that owns this counter.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the users (officers) assigned to this counter's department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'departments_id', 'department_id');
    }

    /**
     * Get the services processed at this counter.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'counter_service');
    }
}
