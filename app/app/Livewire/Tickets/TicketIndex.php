<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $perPage = 15;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $selectedProject = '';
    public $selectedStatus = '';
    public $selectedPriority = '';
    public $selectedAssigned = '';
    public $viewMode = 'table'; // table or card
    public $showMyTickets = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'selectedProject' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'selectedPriority' => ['except' => ''],
        'selectedAssigned' => ['except' => ''],
        'viewMode' => ['except' => 'table'],
        'showMyTickets' => ['except' => false],
    ];

    protected $listeners = [
        'ticketCreated' => '$refresh',
        'ticketUpdated' => '$refresh',
        'ticketDeleted' => '$refresh',
        'ticketStatusChanged' => '$refresh',
    ];

    public function mount()
    {
        $this->authorize('tickets.view');
    }

    public function render()
    {
        $this->authorize('tickets.view');

        $user = auth()->user();
        
        // Get user's accessible projects
        $projects = $user->companies()->with('projects')->get()
            ->pluck('projects')->flatten()->pluck('id')->toArray();

        $ticketsQuery = Ticket::query()
            ->whereIn('project_id', $projects)
            ->with(['project', 'user', 'assignedUser', 'status', 'priority', 'type'])
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('ticket_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedProject, function ($query) {
                $query->where('project_id', $this->selectedProject);
            })
            ->when($this->selectedStatus, function ($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->when($this->selectedPriority, function ($query) {
                $query->where('priority', $this->selectedPriority);
            })
            ->when($this->selectedAssigned, function ($query) {
                $query->where('assigned_to', $this->selectedAssigned);
            })
            ->when($this->showMyTickets, function ($query) use ($user) {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            });

        $tickets = $ticketsQuery->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Get filter options
        $availableProjects = Project::whereIn('id', $projects)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $availableUsers = User::whereHas('companies', function($query) use ($user) {
            $query->whereIn('companies.id', $user->companies()->pluck('companies.id'));
        })->orderBy('name')->get();

        $statusOptions = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'pending' => 'Pending',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'reopened' => 'Reopened'
        ];

        $priorityOptions = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent'
        ];

        return view('livewire.tickets.ticket-index', [
            'tickets' => $tickets,
            'projects' => $availableProjects,
            'users' => $availableUsers,
            'statusOptions' => $statusOptions,
            'priorityOptions' => $priorityOptions,
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

    public function updateTicketStatus($ticketId, $status)
    {
        $this->authorize('tickets.edit');

        $ticket = Ticket::findOrFail($ticketId);
        
        // Check if user has access to this ticket
        $userProjects = auth()->user()->companies()->with('projects')->get()
            ->pluck('projects')->flatten()->pluck('id')->toArray();
        
        if (!in_array($ticket->project_id, $userProjects)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Unauthorized action'
            ]);
            return;
        }

        $ticket->update(['status' => $status]);
        
        $this->dispatch('ticketStatusChanged');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Ticket status updated successfully'
        ]);
    }

    public function updateTicketPriority($ticketId, $priority)
    {
        $this->authorize('tickets.edit');

        $ticket = Ticket::findOrFail($ticketId);
        
        // Check if user has access to this ticket
        $userProjects = auth()->user()->companies()->with('projects')->get()
            ->pluck('projects')->flatten()->pluck('id')->toArray();
        
        if (!in_array($ticket->project_id, $userProjects)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Unauthorized action'
            ]);
            return;
        }

        $ticket->update(['priority' => $priority]);
        
        $this->dispatch('ticketUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Ticket priority updated successfully'
        ]);
    }

    public function assignTicket($ticketId, $userId)
    {
        $this->authorize('tickets.edit');

        $ticket = Ticket::findOrFail($ticketId);
        
        // Check if user has access to this ticket
        $userProjects = auth()->user()->companies()->with('projects')->get()
            ->pluck('projects')->flatten()->pluck('id')->toArray();
        
        if (!in_array($ticket->project_id, $userProjects)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Unauthorized action'
            ]);
            return;
        }

        $ticket->update(['assigned_to' => $userId]);
        
        $this->dispatch('ticketUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Ticket assigned successfully'
        ]);
    }

    public function deleteTicket(Ticket $ticket)
    {
        $this->authorize('tickets.delete');

        // Check if user has access to this ticket
        $userProjects = auth()->user()->companies()->with('projects')->get()
            ->pluck('projects')->flatten()->pluck('id')->toArray();
        
        if (!in_array($ticket->project_id, $userProjects)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Unauthorized action'
            ]);
            return;
        }

        $ticket->delete();
        $this->dispatch('ticketDeleted');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Ticket deleted successfully'
        ]);
    }

    public function getCanCreateProperty()
    {
        return auth()->user()->can('tickets.create');
    }

    public function getCanEditProperty()
    {
        return auth()->user()->can('tickets.edit');
    }

    public function getCanDeleteProperty()
    {
        return auth()->user()->can('tickets.delete');
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function clearFilters()
    {
        $this->reset([
            'search', 'selectedProject', 'selectedStatus', 
            'selectedPriority', 'selectedAssigned', 'showMyTickets'
        ]);
    }
}
