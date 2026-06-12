<?php

declare(strict_types=1);

namespace App\Services\Public;

use App\Events\Public\FeedbackSubmitted;
use App\Exceptions\Public\FeedbackValidationException;
use App\Models\Feedback;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class FeedbackService
{
    /**
     * Validate the queue and user permissions for submitting feedback.
     *
     * @throws FeedbackValidationException
     */
    public function validateQueueForFeedback(int $queueId, User $user): Queue
    {
        $queue = Queue::with(['feedback'])->find($queueId);

        if (! $queue) {
            throw new FeedbackValidationException('Data antrean tidak ditemukan.', 'error', 404);
        }

        // 1. Cek apakah status antrean benar-benar 'Completed' (Selesai)
        if ($queue->status !== 'Completed') {
            throw new FeedbackValidationException('Ulasan hanya dapat diberikan untuk pelayanan yang telah selesai.', 'error', 400);
        }

        // 2. Cek apakah antrean ini sudah pernah diberi feedback (One-review-per-ticket)
        if ($queue->feedback) {
            throw new FeedbackValidationException('Akses Ditolak! Anda sudah mengisi ulasan untuk layanan ini.', 'warning', 403);
        }

        // 3. Hak Akses (RBAC)
        $role = $user->role;
        $roleValue = $role instanceof \BackedEnum ? $role->value : $role;

        if ($roleValue === 'pengunjung') {
            // Pengunjung hanya bisa mengulas antreannya sendiri
            if ($queue->user_id !== $user->id) {
                throw new FeedbackValidationException('Anda tidak memiliki akses untuk mengulas antrean ini.', 'error', 403);
            }
        } elseif ($roleValue === 'admin_fo') {
            // Front Office hanya bisa mengulas atas nama walk-in
            // Walk-in is identified by session_name === 'Walk-In' or booking_code starting with 'WI-'
            $isWalkIn = $queue->session_name === 'Walk-In' || str_starts_with((string) $queue->booking_code, 'WI-');
            if (! $isWalkIn) {
                throw new FeedbackValidationException('Petugas Front Office hanya dapat mengisi ulasan untuk pengunjung walk-in.', 'error', 403);
            }
        } else {
            // Role lain tidak diizinkan mengisi ulasan
            throw new FeedbackValidationException('Peran Anda tidak diizinkan untuk mengisi ulasan.', 'error', 403);
        }

        return $queue;
    }

    /**
     * Store feedback for a queue in a transaction and dispatch event.
     */
    public function storeFeedback(Queue $queue, int $rating, ?string $comment, User $user): Feedback
    {
        return DB::transaction(function () use ($queue, $rating, $comment, $user) {
            $feedback = Feedback::create([
                'queue_id' => $queue->id,
                'user_id' => $user->id,
                'rating' => $rating,
                'comment' => $comment,
            ]);

            // Dispatch event for activity logging
            event(new FeedbackSubmitted($feedback, $queue, $user));

            return $feedback;
        });
    }
}
