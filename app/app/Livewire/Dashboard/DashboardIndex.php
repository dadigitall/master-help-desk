<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardIndex extends Component
{
    use AuthorizesRequests;

    public $period = 'month'; // day, week, month, year
    public $selectedCompany = null;
    public $selectedProject = null;
    public $selectedUser = null;
    public $comparisonMode = false;
    public $refreshInterval = 60; // seconds

    protected $listeners = [
        'refreshDashboard' => '$refresh',
        'periodChanged' => 'updatePeriod',
        'filtersChanged' => 'updateFilters',
    ];

    public function mount()
    {
        $this->authorize('dashboard.view');
        $this->loadUserPreferences();
    }

    public function loadUserPreferences()
    {
        $user = auth()->user();
        $this->period = $user->dashboard_preferences['period'] ?? 'month';
        $this->selectedCompany = $user->dashboard_preferences['company_id'] ?? null;
        $this->selectedProject = $user->dashboard_preferences['project_id'] ?? null;
    }

    public function updatePeriod($period)
    {
        $this->period = $period;
        $this->saveUserPreferences();
    }

    public function updateFilters($filters)
    {
        $this->selectedCompany = $filters['company_id'] ?? null;
        $this->selectedProject = $filters['project_id'] ?? null;
        $this->selectedUser = $filters['user_id'] ?? null;
        $this->saveUserPreferences();
    }

    public function saveUserPreferences()
    {
        $user = auth()->user();
        $preferences = [
            'period' => $this->period,
            'company_id' => $this->selectedCompany,
            'project_id' => $this->selectedProject,
        ];
        $user->update(['dashboard_preferences' => $preferences]);
    }

    public function getDateRange()
    {
        $now = Carbon::now();
        
        switch ($this->period) {
            case 'day':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'previous' => [
                        'start' => $now->copy()->subDay()->startOfDay(),
                        'end' => $now->copy()->subDay()->endOfDay(),
                    ]
                ];
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                    'previous' => [
                        'start' => $now->copy()->subWeek()->startOfWeek(),
                        'end' => $now->copy()->subWeek()->endOfWeek(),
                    ]
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'previous' => [
                        'start' => $now->copy()->subMonth()->startOfMonth(),
                        'end' => $now->copy()->subMonth()->endOfMonth(),
                    ]
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                    'previous' => [
                        'start' => $now->copy()->subYear()->startOfYear(),
                        'end' => $now->copy()->subYear()->endOfYear(),
                    ]
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'previous' => [
                        'start' => $now->copy()->subMonth()->startOfMonth(),
                        'end' => $now->copy()->subMonth()->endOfMonth(),
                    ]
                ];
        }
    }

    public function getBaseQuery()
    {
        $user = auth()->user();
        $query = Ticket::query();

        // Filter by user's accessible companies
        if (!$user->hasRole('admin')) {
            $userCompanyIds = $user->companies()->pluck('companies.id');
            $query->whereHas('project', function($q) use ($userCompanyIds) {
                $q->whereIn('company_id', $userCompanyIds);
            });
        }

        // Apply additional filters
        if ($this->selectedCompany) {
            $query->whereHas('project', function($q) {
                $q->where('company_id', $this->selectedCompany);
            });
        }

        if ($this->selectedProject) {
            $query->where('project_id', $this->selectedProject);
        }

        if ($this->selectedUser) {
            $query->where('assigned_to', $this->selectedUser);
        }

        return $query;
    }

    public function getOverallStatsProperty()
    {
        $cacheKey = "dashboard_stats_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}_{$this->selectedUser}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            $query = $this->getBaseQuery();

            $current = $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            $previous = $this->getBaseQuery()->whereBetween('created_at', [$dateRange['previous']['start'], $dateRange['previous']['end']]);

            return [
                'total_tickets' => [
                    'current' => $current->count(),
                    'previous' => $previous->count(),
                    'growth' => $this->calculateGrowth($current->count(), $previous->count()),
                ],
                'open_tickets' => [
                    'current' => $current->where('status', 'open')->count(),
                    'previous' => $previous->where('status', 'open')->count(),
                    'growth' => $this->calculateGrowth(
                        $current->where('status', 'open')->count(),
                        $previous->where('status', 'open')->count()
                    ),
                ],
                'resolved_tickets' => [
                    'current' => $current->where('status', 'resolved')->count(),
                    'previous' => $previous->where('status', 'resolved')->count(),
                    'growth' => $this->calculateGrowth(
                        $current->where('status', 'resolved')->count(),
                        $previous->where('status', 'resolved')->count()
                    ),
                ],
                'avg_resolution_time' => [
                    'current' => $this->calculateAverageResolutionTime($current),
                    'previous' => $this->calculateAverageResolutionTime($previous),
                    'growth' => $this->calculateTimeGrowth(
                        $this->calculateAverageResolutionTime($current),
                        $this->calculateAverageResolutionTime($previous)
                    ),
                ],
            ];
        });
    }

    public function getTicketStatusDistributionProperty()
    {
        $cacheKey = "dashboard_status_distribution_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}_{$this->selectedUser}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            $query = $this->getBaseQuery()->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

            return $query->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->orderBy('count', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'status' => $item->status,
                        'label' => $this->getStatusLabel($item->status),
                        'count' => $item->count,
                        'percentage' => $this->calculatePercentage($item->count, $query->count()),
                        'color' => $this->getStatusColor($item->status),
                    ];
                })
                ->values();
        });
    }

    public function getPriorityDistributionProperty()
    {
        $cacheKey = "dashboard_priority_distribution_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}_{$this->selectedUser}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            $query = $this->getBaseQuery()->whereBetween('tickets.created_at', [$dateRange['start'], $dateRange['end']]);

            $totalCount = $query->count();

            return $query->selectRaw('COALESCE(tp.name, "unassigned") as priority, COUNT(*) as count')
                ->leftJoin('ticket_priorities as tp', 'tickets.priority_id', '=', 'tp.id')
                ->groupBy('tickets.priority_id', 'tp.name')
                ->orderByRaw('FIELD(tp.name, "urgent", "high", "medium", "low", "unassigned")')
                ->get()
                ->map(function($item) use ($totalCount) {
                    return [
                        'priority' => $item->priority,
                        'label' => ucfirst($item->priority),
                        'count' => $item->count,
                        'percentage' => $this->calculatePercentage($item->count, $totalCount),
                        'color' => $this->getPriorityColor($item->priority),
                    ];
                })
                ->values();
        });
    }

    public function getTimelineDataProperty()
    {
        $cacheKey = "dashboard_timeline_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}_{$this->selectedUser}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            $query = $this->getBaseQuery()->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

            $periods = $this->generatePeriods($dateRange['start'], $dateRange['end']);
            
            $data = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            return $periods->map(function($period) use ($data) {
                return [
                    'date' => $period->format('Y-m-d'),
                    'label' => $period->format($this->getPeriodFormat()),
                    'count' => $data->get($period->format('Y-m-d'))->count ?? 0,
                ];
            })->values();
        });
    }

    public function getTopPerformersProperty()
    {
        $cacheKey = "dashboard_performers_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            
            return User::select('users.id', 'users.name', 'users.email')
                ->selectRaw('COUNT(tickets.id) as resolved_count')
                ->selectRaw('AVG(DATEDIFF(tickets.updated_at, tickets.created_at)) as avg_resolution_days')
                ->join('tickets', 'users.id', '=', 'tickets.assigned_to')
                ->whereBetween('tickets.updated_at', [$dateRange['start'], $dateRange['end']])
                ->where('tickets.status', 'resolved')
                ->whereNotNull('tickets.assigned_to')
                ->groupBy('users.id', 'users.name', 'users.email')
                ->orderBy('resolved_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'resolved_count' => $user->resolved_count,
                        'avg_resolution_days' => round($user->avg_resolution_days, 1),
                        'avatar' => $user->profile_photo_url ?? null,
                    ];
                });
        });
    }

    public function getRecentTicketsProperty()
    {
        return $this->getBaseQuery()
            ->with(['project', 'assignedUser', 'priority'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'title' => $ticket->title,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority?->name ?? 'unassigned',
                    'project_name' => $ticket->project->name,
                    'assigned_to' => $ticket->assignedUser?->name,
                    'created_at' => $ticket->created_at->diffForHumans(),
                    'status_color' => $this->getStatusColor($ticket->status),
                    'priority_color' => $this->getPriorityColor($ticket->priority?->name ?? 'unassigned'),
                ];
            });
    }

    public function getCompaniesProperty()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return Company::orderBy('name')->get();
        }
        
        return $user->companies()->orderBy('name')->get();
    }

    public function getProjectsProperty()
    {
        $query = Project::with('company');
        
        if ($this->selectedCompany) {
            $query->where('company_id', $this->selectedCompany);
        } else {
            $user = auth()->user();
            if (!$user->hasRole('admin')) {
                $userCompanyIds = $user->companies()->pluck('companies.id');
                $query->whereIn('company_id', $userCompanyIds);
            }
        }
        
        return $query->orderBy('name')->get();
    }

    public function getUsersProperty()
    {
        $query = User::orderBy('name');
        
        if ($this->selectedCompany) {
            $query->whereHas('companies', function($q) {
                $q->where('companies.id', $this->selectedCompany);
            });
        }
        
        return $query->get();
    }

    // Helper methods
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function calculateTimeGrowth($current, $previous)
    {
        if ($previous == 0) {
            return 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function calculateAverageResolutionTime($query)
    {
        $resolved = $query->where('status', 'resolved')->get();
        
        if ($resolved->isEmpty()) {
            return 0;
        }
        
        $totalHours = $resolved->sum(function($ticket) {
            return $ticket->created_at->diffInHours($ticket->updated_at);
        });
        
        return round($totalHours / $resolved->count(), 1);
    }

    private function calculatePercentage($value, $total)
    {
        if ($total == 0) {
            return 0;
        }
        
        return round(($value / $total) * 100, 1);
    }

    private function generatePeriods($start, $end)
    {
        $periods = collect();
        $current = $start->copy();
        
        while ($current <= $end) {
            $periods->push($current->copy());
            
            switch ($this->period) {
                case 'day':
                    $current->addDay();
                    break;
                case 'week':
                    $current->addWeek();
                    break;
                case 'month':
                    $current->addMonth();
                    break;
                case 'year':
                    $current->addYear();
                    break;
            }
        }
        
        return $periods;
    }

    private function getPeriodFormat()
    {
        switch ($this->period) {
            case 'day':
                return 'H:i';
            case 'week':
                return 'D M d';
            case 'month':
                return 'M d';
            case 'year':
                return 'M Y';
            default:
                return 'M d';
        }
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'pending' => 'Pending',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'reopened' => 'Reopened',
        ];
        
        return $labels[$status] ?? ucfirst($status);
    }

    private function getStatusColor($status)
    {
        $colors = [
            'open' => '#EF4444',
            'in_progress' => '#F59E0B',
            'pending' => '#6B7280',
            'resolved' => '#10B981',
            'closed' => '#059669',
            'reopened' => '#8B5CF6',
        ];
        
        return $colors[$status] ?? '#6B7280';
    }

    private function getPriorityColor($priority)
    {
        $colors = [
            'low' => '#10B981',
            'medium' => '#F59E0B',
            'high' => '#F97316',
            'urgent' => '#EF4444',
        ];
        
        return $colors[$priority] ?? '#6B7280';
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-index', [
            'overallStats' => $this->overallStats,
            'ticketStatusDistribution' => $this->ticketStatusDistribution,
            'priorityDistribution' => $this->priorityDistribution,
            'timelineData' => $this->timelineData,
            'topPerformers' => $this->topPerformers,
            'recentTickets' => $this->recentTickets,
            'companies' => $this->companies,
            'projects' => $this->projects,
            'users' => $this->users,
        ]);
    }
}
