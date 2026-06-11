<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Model Audit Trail / ActivityLog — mencatat semua aktivitas penting dalam sistem.
 *
 * @property int $id
 * @property int|null $causer_id User yang melakukan aksi (AuditLogger)
 * @property int|null $subject_id ID objek yang dikenai aksi
 * @property string|null $subject_type Nama class model subjek
 * @property string|null $event Nama aksi singkat dari AuditLogger (created, updated, dll.)
 * @property string|null $description Teks human-readable
 * @property array|null $properties
 * @property string|null $ip_address IP address request
 * @property string|null $user_agent User agent browser/client
 * @property Carbon $created_at
 */
class ActivityLog extends Model
{
    /** Tidak ada kolom updated_at pada tabel ini. */
    public const UPDATED_AT = null;

    /** @var array<string, string> */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /** @var list<string> */
    protected $fillable = [
        'causer_id',
        'subject_id',
        'subject_type',
        'event',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    // ──────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────

    /**
     * User yang melakukan aksi (pelaku / causer).
     *
     * @return BelongsTo<User, self>
     */
    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Objek yang dikenai aksi (polymorphic).
     *
     * @return MorphTo<Model, self>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }

    // ──────────────────────────────────────────────────
    // Static Helper
    // ──────────────────────────────────────────────────

    /**
     * Log sebuah aksi ke audit trail (compatible dengan record() design).
     */
    public static function record(
        string $action,
        string $modelType,
        int $modelId,
        ?string $description = null,
        ?int $actorUserId = null
    ): self {
        return self::create([
            'causer_id' => $actorUserId,
            'event' => $action,
            'subject_type' => $modelType,
            'subject_id' => $modelId,
            'description' => $description,
        ]);
    }

    // ──────────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────────

    /** Filter log berdasarkan nama event. */
    public function scopeForEvent($query, string $event): void
    {
        $query->where('event', $event);
    }

    /** Filter log berdasarkan subjek tertentu. */
    public function scopeForSubject($query, Model $subject): void
    {
        $query->where('subject_type', $subject::class)
            ->where('subject_id', $subject->getKey());
    }

    /** Filter log yang dibuat oleh pelaku tertentu. */
    public function scopeByCauser($query, User $causer): void
    {
        $query->where('causer_id', $causer->id);
    }
}
