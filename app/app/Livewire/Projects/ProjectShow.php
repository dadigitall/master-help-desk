<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\FavoriteProject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectShow extends Component
{
    use AuthorizesRequests;

    public $showModal = false;
    public $project;
    public $isFavorite = false;

    protected $listeners = [
        'openViewProjectModal' => 'openModal',
    ];

    public function openModal($projectId)
    {
        $this->authorize('projects.view');

        $this->project = Project::with([
            'company',
            'user',
            'tickets' => function($query) {
                $query->latest()->limit(10);
            },
            'tickets.user',
            'tickets.status'
        ])->findOrFail($projectId);

        $this->isFavorite = auth()->user()->favoriteProjects()
            ->where('project_id', $projectId)
            ->exists();

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
    }

    public function toggleFavorite()
    {
        $user = auth()->user();
        $favorite = FavoriteProject::where('user_id', $user->id)
            ->where('project_id', $this->project->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $this->isFavorite = false;
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Removed from favorites'
            ]);
        } else {
            FavoriteProject::create([
                'user_id' => $user->id,
                'project_id' => $this->project->id,
            ]);
            $this->isFavorite = true;
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Added to favorites'
            ]);
        }

        $this->dispatch('favoriteToggled');
    }

    public function getTicketsStatsProperty()
    {
        if (!$this->project) {
            return collect();
        }

        return $this->project->tickets()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
    }

    public function getTicketsByPriorityProperty()
    {
        if (!$this->project) {
            return collect();
        }

        return $this->project->tickets()
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');
    }

    public function getRecentTicketsProperty()
    {
        if (!$this->project) {
            return collect();
        }

        return $this->project->tickets()
            ->with(['user', 'status'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getCompletionPercentageProperty()
    {
        if (!$this->project) {
            return 0;
        }

        $total = $this->project->tickets_count;
        if ($total === 0) {
            return 0;
        }

        $closed = $this->project->tickets()->where('status', 'closed')->count();
        return round(($closed / $total) * 100);
    }

    public function render()
    {
        return view('livewire.projects.project-show', [
            'ticketsStats' => $this->ticketsStats,
            'ticketsByPriority' => $this->ticketsByPriority,
            'recentTickets' => $this->recentTickets,
            'completionPercentage' => $this->completionPercentage,
        ]);
    }
}
