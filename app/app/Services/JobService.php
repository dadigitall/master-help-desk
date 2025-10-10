<?php

namespace App\Services;

use App\Jobs\SendTicketNotificationJob;
use App\Jobs\GenerateReportJob;
use App\Jobs\CleanupOldDataJob;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class JobService
{
    /**
     * Dispatch a ticket notification job.
     */
    public function sendTicketNotification(Ticket $ticket, User $user, string $action, ?string $message = null): void
    {
        try {
            SendTicketNotificationJob::dispatch($ticket, $user, $action, $message);
            
            Log::info('Ticket notification job dispatched', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'action' => $action
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch ticket notification job', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Dispatch a report generation job.
     */
    public function generateReport(User $user, string $reportType, ?array $filters = null, ?string $format = 'csv'): void
    {
        try {
            GenerateReportJob::dispatch($user, $reportType, $filters, $format);
            
            Log::info('Report generation job dispatched', [
                'user_id' => $user->id,
                'report_type' => $reportType,
                'format' => $format
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch report generation job', [
                'user_id' => $user->id,
                'report_type' => $reportType,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Dispatch a cleanup job.
     */
    public function cleanupOldData(?int $daysToKeep = null): void
    {
        try {
            CleanupOldDataJob::dispatch($daysToKeep);
            
            Log::info('Cleanup job dispatched', [
                'days_to_keep' => $daysToKeep
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch cleanup job', [
                'days_to_keep' => $daysToKeep,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notifications for ticket creation.
     */
    public function notifyTicketCreated(Ticket $ticket): void
    {
        // Notify assigned user if any
        if ($ticket->assignedTo) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->assignedTo,
                'assigned',
                "Un nouveau ticket vous a été assigné : {$ticket->title}"
            );
        }

        // Notify company managers if applicable
        if ($ticket->company) {
            $managers = $this->getCompanyManagers($ticket->company);
            foreach ($managers as $manager) {
                $this->sendTicketNotification(
                    $ticket,
                    $manager,
                    'created',
                    "Un nouveau ticket a été créé pour votre entreprise : {$ticket->title}"
                );
            }
        }
    }

    /**
     * Send notifications for ticket update.
     */
    public function notifyTicketUpdated(Ticket $ticket, ?string $message = null): void
    {
        // Notify assigned user if any and not the updater
        if ($ticket->assignedTo && $ticket->assignedTo->id !== auth()->id()) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->assignedTo,
                'updated',
                $message ?? "Le ticket #{$ticket->id} a été mis à jour"
            );
        }

        // Notify ticket creator if not the updater
        if ($ticket->user && $ticket->user->id !== auth()->id()) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->user,
                'updated',
                $message ?? "Votre ticket #{$ticket->id} a été mis à jour"
            );
        }
    }

    /**
     * Send notifications for ticket assignment.
     */
    public function notifyTicketAssigned(Ticket $ticket, User $assignedTo): void
    {
        $this->sendTicketNotification(
            $ticket,
            $assignedTo,
            'assigned',
            "Le ticket '{$ticket->title}' vous a été assigné"
        );

        // Notify ticket creator if not the assigned user
        if ($ticket->user && $ticket->user->id !== $assignedTo->id) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->user,
                'assigned',
                "Votre ticket #{$ticket->id} a été assigné à {$assignedTo->name}"
            );
        }
    }

    /**
     * Send notifications for ticket closure.
     */
    public function notifyTicketClosed(Ticket $ticket, ?string $message = null): void
    {
        // Notify assigned user if any and not the closer
        if ($ticket->assignedTo && $ticket->assignedTo->id !== auth()->id()) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->assignedTo,
                'closed',
                $message ?? "Le ticket #{$ticket->id} a été fermé"
            );
        }

        // Notify ticket creator if not the closer
        if ($ticket->user && $ticket->user->id !== auth()->id()) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->user,
                'closed',
                $message ?? "Votre ticket #{$ticket->id} a été fermé"
            );
        }
    }

    /**
     * Send notifications for ticket reopening.
     */
    public function notifyTicketReopened(Ticket $ticket, ?string $message = null): void
    {
        // Notify assigned user if any and not the reopener
        if ($ticket->assignedTo && $ticket->assignedTo->id !== auth()->id()) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->assignedTo,
                'reopened',
                $message ?? "Le ticket #{$ticket->id} a été rouvert"
            );
        }

        // Notify ticket creator if not the reopener
        if ($ticket->user && $ticket->user->id !== auth()->id()) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->user,
                'reopened',
                $message ?? "Votre ticket #{$ticket->id} a été rouvert"
            );
        }
    }

    /**
     * Send notifications for new comment.
     */
    public function notifyCommentAdded(Ticket $ticket, User $commentAuthor, string $comment): void
    {
        // Notify assigned user if any and not the comment author
        if ($ticket->assignedTo && $ticket->assignedTo->id !== $commentAuthor->id) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->assignedTo,
                'comment_added',
                "Nouveau commentaire de {$commentAuthor->name} sur le ticket #{$ticket->id}"
            );
        }

        // Notify ticket creator if not the comment author
        if ($ticket->user && $ticket->user->id !== $commentAuthor->id) {
            $this->sendTicketNotification(
                $ticket,
                $ticket->user,
                'comment_added',
                "Nouveau commentaire de {$commentAuthor->name} sur votre ticket #{$ticket->id}"
            );
        }
    }

    /**
     * Get company managers who should receive notifications.
     */
    private function getCompanyManagers($company): array
    {
        return $company->users()
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Administrator', 'Employee']);
            })
            ->get()
            ->all();
    }

    /**
     * Get available report types.
     */
    public function getAvailableReportTypes(): array
    {
        return [
            'tickets' => 'Rapport des tickets',
            'users' => 'Rapport des utilisateurs',
            'companies' => 'Rapport des entreprises',
            'performance' => 'Rapport de performance'
        ];
    }

    /**
     * Get available report formats.
     */
    public function getAvailableReportFormats(): array
    {
        return [
            'csv' => 'CSV',
            'json' => 'JSON',
            'pdf' => 'PDF (bientôt disponible)'
        ];
    }
}
