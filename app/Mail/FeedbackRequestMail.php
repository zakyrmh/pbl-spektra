<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Queue $queueModel;

    /**
     * Create a new message instance.
     */
    public function __construct(Queue $queue)
    {
        $this->queueModel = $queue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pelayanan Selesai - Berikan Ulasan Anda ('.$this->queueModel->queue_number.')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback_request',
            with: [
                'queue' => $this->queueModel,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
