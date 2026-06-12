<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Notification extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'user_id',
        'title',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Notification $notification) {
            if (empty($notification->id)) {
                $notification->id = (string) Str::uuid();
            }

            if (empty($notification->type)) {
                $notification->type = 'App\Notifications\CustomNotification';
            }

            if (empty($notification->notifiable_type)) {
                $notification->notifiable_type = 'App\Models\User';
            }

            if (empty($notification->notifiable_id) && ! empty($notification->user_id)) {
                $notification->notifiable_id = $notification->user_id;
            }

            if (empty($notification->data)) {
                $notification->data = [
                    'title' => $notification->title,
                    'message' => $notification->message,
                ];
            }
        });
    }

    /**
     * Get the user that owns the notification.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
