<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private User $user,
        private string $reportType,
        private ?array $filters = null,
        private ?string $format = 'csv'
    ) {
        $this->onQueue('reports');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $filename = $this->generateReport();
            
            Log::info('Report generated successfully', [
                'user_id' => $this->user->id,
                'report_type' => $this->reportType,
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate report', [
                'user_id' => $this->user->id,
                'report_type' => $this->reportType,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Generate the report based on type.
     */
    private function generateReport(): string
    {
        return match($this->reportType) {
            'tickets' => $this->generateTicketsReport(),
            'users' => $this->generateUsersReport(),
            'companies' => $this->generateCompaniesReport(),
            'performance' => $this->generatePerformanceReport(),
            default => throw new \InvalidArgumentException("Unknown report type: {$this->reportType}")
        };
    }

    /**
     * Generate tickets report.
     */
    private function generateTicketsReport(): string
    {
        $query = Ticket::with(['user', 'assignedTo', 'company', 'project', 'status', 'priority', 'type']);

        if ($this->filters) {
            if (isset($this->filters['date_from'])) {
                $query->where('created_at', '>=', $this->filters['date_from']);
            }
            if (isset($this->filters['date_to'])) {
                $query->where('created_at', '<=', $this->filters['date_to']);
            }
            if (isset($this->filters['status_id'])) {
                $query->where('status_id', $this->filters['status_id']);
            }
            if (isset($this->filters['priority_id'])) {
                $query->where('priority_id', $this->filters['priority_id']);
            }
            if (isset($this->filters['company_id'])) {
                $query->where('company_id', $this->filters['company_id']);
            }
        }

        $tickets = $query->get();

        $filename = "tickets_report_" . Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
        $path = "reports/{$filename}";

        $csv = "ID,Titre,Utilisateur,Assigné à,Entreprise,Projet,Statut,Priorité,Type,Créé le,Modifié le\n";

        foreach ($tickets as $ticket) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $ticket->id,
                str_replace('"', '""', $ticket->title),
                $ticket->user?->name ?? 'N/A',
                $ticket->assignedTo?->name ?? 'N/A',
                $ticket->company?->name ?? 'N/A',
                $ticket->project?->name ?? 'N/A',
                $ticket->status?->name ?? 'N/A',
                $ticket->priority?->name ?? 'N/A',
                $ticket->type?->name ?? 'N/A',
                $ticket->created_at->format('Y-m-d H:i:s'),
                $ticket->updated_at->format('Y-m-d H:i:s')
            );
        }

        Storage::disk('local')->put($path, $csv);

        return $filename;
    }

    /**
     * Generate users report.
     */
    private function generateUsersReport(): string
    {
        $query = User::with(['roles', 'companies']);

        if ($this->filters && isset($this->filters['role'])) {
            $query->whereHas('roles', function($q) {
                $q->where('name', $this->filters['role']);
            });
        }

        $users = $query->get();

        $filename = "users_report_" . Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
        $path = "reports/{$filename}";

        $csv = "ID,Nom,Email,Rôles,Entreprises,Créé le,Dernière connexion\n";

        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->implode(', ');
            $companies = $user->companies->pluck('name')->implode(', ');
            
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $user->id,
                str_replace('"', '""', $user->name),
                $user->email,
                $roles,
                $companies,
                $user->created_at->format('Y-m-d H:i:s'),
                $user->last_login_at?->format('Y-m-d H:i:s') ?? 'Never'
            );
        }

        Storage::disk('local')->put($path, $csv);

        return $filename;
    }

    /**
     * Generate companies report.
     */
    private function generateCompaniesReport(): string
    {
        $companies = Company::withCount(['projects', 'tickets', 'users'])->get();

        $filename = "companies_report_" . Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
        $path = "reports/{$filename}";

        $csv = "ID,Nom,Description,Projets,Tickets,Utilisateurs,Créé le\n";

        foreach ($companies as $company) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",%d,%d,%d,\"%s\"\n",
                $company->id,
                str_replace('"', '""', $company->name),
                str_replace('"', '""', $company->description ?? ''),
                $company->projects_count,
                $company->tickets_count,
                $company->users_count,
                $company->created_at->format('Y-m-d H:i:s')
            );
        }

        Storage::disk('local')->put($path, $csv);

        return $filename;
    }

    /**
     * Generate performance report.
     */
    private function generatePerformanceReport(): string
    {
        $dateFrom = $this->filters['date_from'] ?? Carbon::now()->subMonth();
        $dateTo = $this->filters['date_to'] ?? Carbon::now();

        $tickets = Ticket::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['status', 'priority'])
            ->get();

        $stats = [
            'total_tickets' => $tickets->count(),
            'by_status' => $tickets->groupBy('status.name')->map->count(),
            'by_priority' => $tickets->groupBy('priority.name')->map->count(),
            'avg_resolution_time' => $tickets->whereNotNull('closed_at')
                ->avg(function($ticket) {
                    return $ticket->created_at->diffInHours($ticket->closed_at);
                })
        ];

        $filename = "performance_report_" . Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
        $path = "reports/{$filename}";

        $csv = "Métrique,Valeur\n";
        $csv .= "Total des tickets,{$stats['total_tickets']}\n";
        $csv .= "Temps de résolution moyen (heures)," . round($stats['avg_resolution_time'] ?? 0, 2) . "\n\n";

        $csv .= "Tickets par statut\n";
        foreach ($stats['by_status'] as $status => $count) {
            $csv .= "\"{$status}\",{$count}\n";
        }

        $csv .= "\nTickets par priorité\n";
        foreach ($stats['by_priority'] as $priority => $count) {
            $csv .= "\"{$priority}\",{$count}\n";
        }

        Storage::disk('local')->put($path, $csv);

        return $filename;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateReportJob failed', [
            'user_id' => $this->user->id,
            'report_type' => $this->reportType,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'report',
            'report-type:' . $this->reportType,
            'user:' . $this->user->id
        ];
    }
}
