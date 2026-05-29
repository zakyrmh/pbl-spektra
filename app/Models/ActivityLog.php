<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ActivityLog — append-only audit trail.
 * AGENT.md §4.3: Tidak ada updated_at; tidak bisa dihapus kecuali super_admin.
 */
class ActivityLog extends Model
{
    // Tidak ada updated_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
    ];

    /**
     * Log sebuah aksi ke audit trail.
     */
    public static function record(
        string $action,
        string $modelType,
        int $modelId,
        ?string $description = null,
        ?int $actorUserId = null
    ): self {
        return self::create([
            'user_id' => $actorUserId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
        ]);
    }
}
