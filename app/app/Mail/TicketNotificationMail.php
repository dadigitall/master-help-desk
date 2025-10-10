<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private Ticket $ticket,
        private User $user,
        private string $action,
        private ?string $message = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->action) {
            'created' => 'Nouveau ticket créé',
            'updated' => 'Ticket mis à jour',
            'assigned' => 'Ticket assigné',
            'closed' => 'Ticket fermé',
            'reopened' => 'Ticket rouvert',
            'comment_added' => 'Nouveau commentaire sur ticket',
            default => 'Notification ticket'
        };

        return new Envelope(
            subject: "[Help Desk] {$subject} - #{$this->ticket->id}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-notification',
            with: [
                'ticket' => $this->ticket,
                'user' => $this->user,
                'action' => $this->action,
                'message' => $this->message,
                'actionLabel' => match($this->action) {
                    'created' => 'Créé',
                    'updated' => 'Mis à jour',
                    'assigned' => 'Assigné',
                    'closed' => 'Fermé',
                    'reopened' => 'Rouvert',
                    'comment_added' => 'Commentaire ajouté',
                    default => $this->action
                }
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
