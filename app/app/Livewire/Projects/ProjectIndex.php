<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Company;
use App\Models\FavoriteProject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $perPage = 12;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $showInactive = false;
    public $selectedCompany = '';
    public $viewMode = 'grid'; // grid or list
    public $showFavoritesOnly = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 12],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'showInactive' => ['except' => false],
        'selectedCompany' => ['except' => ''],
        'viewMode' => ['except' => 'grid'],
        'showFavoritesOnly' => ['except' => false],
    ];

    protected $listeners = [
        'projectCreated' => '$refresh',
        'projectUpdated' => '$refresh',
        'projectDeleted' => '$refresh',
        'favoriteToggled' => '$refresh',
    ];

    public function mount()
    {
        $this->authorize('projects.view');
    }

    public function render()
    {
        $this->authorize('projects.view');

        $user = auth()->user();
        
        // Get user's companies for filtering
        $companies = $user->companies()->pluck('id')->toArray();
        
        $projectsQuery = Project::query()
            ->whereIn('company_id', $companies)
            ->with(['company', 'user', 'tickets', 'favoriteProjects' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('prefix', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCompany, function ($query) {
                $query->where('company_id', $this->selectedCompany);
            })
            ->when(!$this->showInactive, function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->showFavoritesOnly, function ($query) use ($user) {
                $query->whereHas('favoriteProjects', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            })
            ->withCount(['tickets', 'tickets as open_tickets_count' => function($query) {
                $query->where('status', 'open');
            }]);

        $projects = $projectsQuery->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Get companies for filter dropdown
        $availableCompanies = Company::whereIn('id', $companies)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.projects.project-index', [
            'projects' => $projects,
            'companies' => $availableCompanies,
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive(Project $project)
    {
        $this->authorize('projects.edit');

        $project->update([
            'is_active' => !$project->is_active,
        ]);

        $this->dispatch('projectUpdated');
    }

    public function toggleFavorite($projectId)
    {
        $user = auth()->user();
        $favorite = FavoriteProject::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Removed from favorites'
            ]);
        } else {
            FavoriteProject::create([
                'user_id' => $user->id,
                'project_id' => $projectId,
            ]);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Added to favorites'
            ]);
        }

        $this->dispatch('favoriteToggled');
    }

    public function deleteProject(Project $project)
    {
        $this->authorize('projects.delete');

        if ($project->tickets()->exists()) {
            $this->addError('delete', 'Cannot delete project with existing tickets.');
            return;
        }

        $project->delete();
        $this->dispatch('projectDeleted');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Project deleted successfully'
        ]);
    }

    public function getCanCreateProperty()
    {
        return auth()->user()->can('projects.create');
    }

    public function getCanEditProperty()
    {
        return auth()->user()->can('projects.edit');
    }

    public function getCanDeleteProperty()
    {
        return auth()->user()->can('projects.delete');
    }

    public function isFavorite($projectId)
    {
        return auth()->user()->favoriteProjects()
            ->where('project_id', $projectId)
            ->exists();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }
}
