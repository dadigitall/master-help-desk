<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\Company;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class DashboardCharts extends Component
{
    use AuthorizesRequests;

    public $period = 'month';
    public $selectedCompany = null;
    public $selectedProject = null;
    public $chartType = 'line'; // line, bar, area
    public $refreshInterval = 60;

    protected $listeners = [
        'refreshCharts' => '$refresh',
        'periodChanged' => 'updatePeriod',
        'filtersChanged' => 'updateFilters',
        'chartTypeChanged' => 'updateChartType',
    ];

    public function mount()
    {
        $this->authorize('dashboard.stats');
    }

    public function updatePeriod($period)
    {
        $this->period = $period;
    }

    public function updateFilters($filters)
    {
        $this->selectedCompany = $filters['company_id'] ?? null;
        $this->selectedProject = $filters['project_id'] ?? null;
    }

    public function updateChartType($type)
    {
        $this->chartType = $type;
    }

    public function getDateRange()
    {
        $now = Carbon::now();
        
        switch ($this->period) {
            case 'day':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                ];
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
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

        return $query;
    }

    public function getTicketTrendsProperty()
    {
        $cacheKey = "chart_trends_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            $query = $this->getBaseQuery()->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

            $periods = $this->generatePeriods($dateRange['start'], $dateRange['end']);
            
            $createdData = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $resolvedData = $this->getBaseQuery()
                ->whereBetween('updated_at', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'resolved')
                ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            return [
                'labels' => $periods->map(function($period) {
                    return $period->format($this->getPeriodFormat());
                })->toArray(),
                'created' => $periods->map(function($period) use ($createdData) {
                    return $createdData->get($period->format('Y-m-d'))->count ?? 0;
                })->toArray(),
                'resolved' => $periods->map(function($period) use ($resolvedData) {
                    return $resolvedData->get($period->format('Y-m-d'))->count ?? 0;
                })->toArray(),
            ];
        });
    }

    public function getProjectPerformanceProperty()
    {
        $cacheKey = "chart_projects_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            
            $query = $this->getBaseQuery()
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->with('project');

            $projectStats = $query->get()
                ->groupBy('project_id')
                ->map(function($tickets, $projectId) {
                    $project = $tickets->first()->project;
                    $resolved = $tickets->where('status', 'resolved')->count();
                    $total = $tickets->count();
                    
                    return [
                        'name' => $project->name,
                        'total' => $total,
                        'resolved' => $resolved,
                        'pending' => $total - $resolved,
                        'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 1) : 0,
                        'avg_resolution_time' => $this->calculateProjectResolutionTime($tickets),
                    ];
                })
                ->sortByDesc('total')
                ->take(10)
                ->values();

            return [
                'labels' => $projectStats->pluck('name')->toArray(),
                'total' => $projectStats->pluck('total')->toArray(),
                'resolved' => $projectStats->pluck('resolved')->toArray(),
                'pending' => $projectStats->pluck('pending')->toArray(),
                'resolution_rates' => $projectStats->pluck('resolution_rate')->toArray(),
            ];
        });
    }

    public function getPriorityAnalysisProperty()
    {
        $cacheKey = "chart_priority_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            $query = $this->getBaseQuery()->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

            $priorityData = $query->selectRaw('priority, COUNT(*) as total, SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved')
                ->groupBy('priority')
                ->orderByRaw('FIELD(priority, "urgent", "high", "medium", "low")')
                ->get();

            return [
                'labels' => $priorityData->map(function($item) {
                    return ucfirst($item->priority);
                })->toArray(),
                'total' => $priorityData->pluck('total')->toArray(),
                'resolved' => $priorityData->pluck('resolved')->toArray(),
                'pending' => $priorityData->map(function($item) {
                    return $item->total - $item->resolved;
                })->toArray(),
                'colors' => [
                    '#EF4444', // urgent - red
                    '#F97316', // high - orange
                    '#F59E0B', // medium - yellow
                    '#10B981', // low - green
                ],
            ];
        });
    }

    public function getResponseTimeAnalysisProperty()
    {
        $cacheKey = "chart_response_time_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            
            $tickets = $this->getBaseQuery()
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'resolved')
                ->get();

            if ($tickets->isEmpty()) {
                return [
                    'labels' => [],
                    'data' => [],
                    'average' => 0,
                ];
            }

            $responseTimes = $tickets->map(function($ticket) {
                $hours = $ticket->created_at->diffInHours($ticket->updated_at);
                return [
                    'ticket_number' => $ticket->ticket_number,
                    'hours' => $hours,
                    'date' => $ticket->created_at->format('Y-m-d'),
                ];
            })->sortBy('hours');

            // Group by response time ranges
            $ranges = [
                '< 1 hour' => $responseTimes->where('hours', '<', 1)->count(),
                '1-6 hours' => $responseTimes->whereBetween('hours', [1, 6])->count(),
                '6-24 hours' => $responseTimes->whereBetween('hours', [6, 24])->count(),
                '1-3 days' => $responseTimes->whereBetween('hours', [24, 72])->count(),
                '> 3 days' => $responseTimes->where('hours', '>', 72)->count(),
            ];

            return [
                'labels' => array_keys($ranges),
                'data' => array_values($ranges),
                'average' => round($responseTimes->avg('hours'), 1),
                'total_tickets' => $tickets->count(),
            ];
        });
    }

    public function getWorkloadDistributionProperty()
    {
        $cacheKey = "chart_workload_{$this->period}_{$this->selectedCompany}_{$this->selectedProject}";
        
        return Cache::remember($cacheKey, $this->refreshInterval, function() {
            $dateRange = $this->getDateRange();
            
            $workloadData = $this->getBaseQuery()
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->whereNotNull('assigned_to')
                ->with('assignedUser')
                ->selectRaw('assigned_to, COUNT(*) as total, SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved')
                ->groupBy('assigned_to')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            return [
                'labels' => $workloadData->map(function($item) {
                    return $item->assignedUser->name;
                })->toArray(),
                'assigned' => $workloadData->pluck('total')->toArray(),
                'resolved' => $workloadData->pluck('resolved')->toArray(),
                'pending' => $workloadData->map(function($item) {
                    return $item->total - $item->resolved;
                })->toArray(),
            ];
        });
    }

    // Helper methods
    private function generatePeriods($start, $end)
    {
        $periods = collect();
        $current = $start->copy();
        
        while ($current <= $end) {
            $periods->push($current->copy());
            
            switch ($this->period) {
                case 'day':
                    $current->addHours(4); // 6 periods per day
                    break;
                case 'week':
                    $current->addDay(); // 7 periods per week
                    break;
                case 'month':
                    $current->addDays(2); // ~15 periods per month
                    break;
                case 'year':
                    $current->addMonth(); // 12 periods per year
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

    private function calculateProjectResolutionTime($tickets)
    {
        $resolved = $tickets->where('status', 'resolved');
        
        if ($resolved->isEmpty()) {
            return 0;
        }
        
        $totalHours = $resolved->sum(function($ticket) {
            return $ticket->created_at->diffInHours($ticket->updated_at);
        });
        
        return round($totalHours / $resolved->count(), 1);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-charts', [
            'ticketTrends' => $this->ticketTrends,
            'projectPerformance' => $this->projectPerformance,
            'priorityAnalysis' => $this->priorityAnalysis,
            'responseTimeAnalysis' => $this->responseTimeAnalysis,
            'workloadDistribution' => $this->workloadDistribution,
        ]);
    }
}
