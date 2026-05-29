<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'inisial',
        'logo',
        'description',
    ];

    /**
     * Get all counters (loket) for this department.
     */
    public function counters(): HasMany
    {
        return $this->hasMany(Counter::class);
    }

    /**
     * Get all services (layanan) for this department.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
