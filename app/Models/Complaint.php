<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'subject', 'category', 'content'])]
class Complaint extends Model
{
    /**
     * Get the user who submitted the complaint.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
