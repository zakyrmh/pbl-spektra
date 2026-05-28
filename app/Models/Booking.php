<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_code',
        'status',
        'booking_date',
        'checked_in_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'checked_in_at' => 'datetime',
    ];

    // ---------------------------------------------------------------
    // Relasi
    // ---------------------------------------------------------------

    /**
     * Warga / Customer pemilik booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ---------------------------------------------------------------
    // Helper
    // ---------------------------------------------------------------

    /**
     * Apakah booking ini bisa di-check-in?
     * Hanya booking berstatus 'Pending' yang valid.
     */
    public function canBeCheckedIn(): bool
    {
        return $this->status === 'Pending';
    }
}
