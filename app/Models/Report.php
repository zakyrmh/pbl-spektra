<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'start_date',
        'end_date',
        'data_summary',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'data_summary' => 'array',
    ];

    /**
     * Mengecek apakah laporan sudah dikirim (dikunci).
     */
    public function isLocked(): bool
    {
        return $this->status === 'Terkirim';
    }

    /**
     * Hubungan ke pembuat laporan (Admin FO).
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
