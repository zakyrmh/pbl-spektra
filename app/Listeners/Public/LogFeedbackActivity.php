<?php

declare(strict_types=1);

namespace App\Listeners\Public;

use App\Events\Public\FeedbackSubmitted;
use App\Models\ActivityLog;

final class LogFeedbackActivity
{
    /**
     * Handle the event.
     */
    public function handle(FeedbackSubmitted $event): void
    {
        $role = $event->user->role;
        $roleValue = $role instanceof \BackedEnum ? $role->value : $role;
        $actorName = $roleValue === 'admin_fo' ? 'Petugas Front Office (atas nama walk-in)' : 'Pengunjung';

        ActivityLog::record(
            action: 'SUBMIT_FEEDBACK',
            modelType: 'Feedback',
            modelId: $event->feedback->id,
            description: "{$actorName} memberikan rating bintang {$event->feedback->rating} untuk nomor antrean {$event->queue->queue_number}.",
            actorUserId: $event->user->id
        );
    }
}
