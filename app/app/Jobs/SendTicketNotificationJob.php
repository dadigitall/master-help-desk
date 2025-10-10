<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TicketNotificationMail;

class SendTicketNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Ticket $ticket,
        private User $user,
        private string $action,
        private ?string $message = null
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->user->email)
                ->send(new TicketNotificationMail(
                    $this->ticket,
                    $this->user,
                    $this->action,
                    $this->message
                ));

            Log::info('Ticket notification sent', [
                'ticket_id' => $this->ticket->id,
                'user_id' => $this->user->id,
                'action' => $this->action,
                'email' => $this->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send ticket notification', [
                'ticket_id' => $this->ticket->id,
                'user_id' => $this->user->id,
                'action' => $this->action,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendTicketNotificationJob failed', [
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'action' => $this->action,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'ticket-notification',
            'ticket:' . $this->ticket->id,
            'user:' . $this->user->id,
            'action:' . $this->action
        ];
    }
}
