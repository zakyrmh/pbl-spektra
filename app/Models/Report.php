<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'created_by',
        'title',
        'start_date',
        'end_date',
        'data_summary',
        'status',
    ];

    /**
     * Cast kolom ke tipe data yang sesuai.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'data_summary' => 'array',
        ];
    }

    /**
     * User/Admin FO yang memicu pembuatan laporan ini.
     *
     * @return BelongsTo<User, self>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Cek apakah laporan telah dikunci (status Terkirim).
     */
    public function isLocked(): bool
    {
        return $this->status === 'Terkirim';
    }
}
