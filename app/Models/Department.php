<?php

namespace App\Models;

use App\Enums\BoothStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[Fillable(['name', 'inisial', 'nomor_loket', 'logo', 'description', 'is_open'])]
class Department extends Model
{
    protected $casts = [
        'is_open' => 'boolean',
    ];

    /**
     * Get the booth operational status.
     */
    public function getStatusAttribute(): BoothStatus
    {
        $cached = Cache::get("loket_status_{$this->id}");
        if ($cached) {
            if ($cached === 'aktif' || $cached === 'buka') {
                return BoothStatus::Buka;
            }
            if ($cached === 'nonaktif' || $cached === 'tutup') {
                return BoothStatus::Tutup;
            }

            return BoothStatus::tryFrom((string) $cached) ?? BoothStatus::Buka;
        }

        return $this->is_open ? BoothStatus::Buka : BoothStatus::Tutup;
    }

    /**
     * Set the booth operational status.
     */
    public function setStatusAttribute(BoothStatus|string $value): void
    {
        $statusEnum = $value instanceof BoothStatus ? $value : BoothStatus::tryFrom((string) $value);

        if ($statusEnum) {
            Cache::put("loket_status_{$this->id}", $statusEnum->value, now()->addDay());
            $this->is_open = ($statusEnum === BoothStatus::Buka);
        }
    }

    /**
     * Get the queues for this department.
     *
     * @return HasMany<Queue, $this>
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    /**
     * Get the users (officers) belonging to this department.
     *
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
