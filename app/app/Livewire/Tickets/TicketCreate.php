<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketCreate extends Component
{
    use AuthorizesRequests;

    public $showModal = false;
    public $title = '';
    public $description = '';
    public $project_id = '';
    public $priority = 'medium';
    public $type = 'bug';
    public $assigned_to = '';
    public $status = 'open';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|min:10',
        'project_id' => 'required|exists:projects,id',
        'priority' => 'required|in:low,medium,high,urgent',
        'type' => 'required|in:bug,feature,improvement,support,other',
        'assigned_to' => 'nullable|exists:users,id',
        'status' => 'required|in:open,in_progress,pending,resolved,closed,reopened',
    ];

    protected $messages = [
        'title.required' => 'The ticket title is required.',
        'title.max' => 'The ticket title may not be greater than 255 characters.',
        'description.required' => 'The ticket description is required.',
        'description.min' => 'The description must be at least 10 characters.',
        'project_id.required' => 'Please select a project.',
        'project_id.exists' => 'The selected project is invalid.',
        'priority.required' => 'Please select a priority level.',
        'priority.in' => 'Invalid priority level selected.',
        'type.required' => 'Please select a ticket type.',
        'type.in' => 'Invalid ticket type selected.',
        'assigned_to.exists' => 'The selected user is invalid.',
        'status.required' => 'Please select a status.',
        'status.in' => 'Invalid status selected.',
    ];

    protected $listeners = [
        'openCreateTicketModal' => 'openModal',
    ];

    public function openModal()
    {
        $this->authorize('tickets.create');

        $this->reset();
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->resetErrorBag();
    }

    public function generateTicketNumber($projectId)
    {
        $project = Project::find($projectId);
        if (!$project) {
            return '';
        }

        // Get the next ticket number for this project
        $lastTicket = Ticket::where('project_id', $projectId)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastTicket ? $lastTicket->id + 1 : 1;
        
        return $project->prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->authorize('tickets.create');

        $validated = $this->validate();

        try {
            // Generate ticket number
            $ticketNumber = $this->generateTicketNumber($validated['project_id']);

            $ticket = Ticket::create([
                ...$validated,
                'ticket_number' => $ticketNumber,
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('ticketCreated');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Ticket created successfully! Ticket number: ' . $ticketNumber
            ]);

            $this->closeModal();
        } catch (\Exception $e) {
            $this->addError('create', 'Failed to create ticket. Please try again.');
        }
    }

    public function updatedProjectId()
    {
        // Reset assigned user when project changes
        $this->assigned_to = '';
    }

    public function getProjectsProperty()
    {
        return auth()->user()->companies()
            ->with('projects')
            ->get()
            ->pluck('projects')
            ->flatten()
            ->where('is_active', true)
            ->sortBy('name');
    }

    public function getUsersProperty()
    {
        if (!$this->project_id) {
            return collect();
        }

        // Get users who have access to the selected project
        $project = Project::find($this->project_id);
        if (!$project) {
            return collect();
        }

        return User::whereHas('companies', function($query) use ($project) {
            $query->where('companies.id', $project->company_id);
        })->orderBy('name')->get();
    }

    public function getPriorityOptionsProperty()
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent'
        ];
    }

    public function getTypeOptionsProperty()
    {
        return [
            'bug' => 'Bug',
            'feature' => 'Feature Request',
            'improvement' => 'Improvement',
            'support' => 'Support',
            'other' => 'Other'
        ];
    }

    public function getStatusOptionsProperty()
    {
        return [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'pending' => 'Pending',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'reopened' => 'Reopened'
        ];
    }

    public function getTicketNumberPreviewProperty()
    {
        if (!$this->project_id) {
            return '';
        }

        return $this->generateTicketNumber($this->project_id);
    }

    public function render()
    {
        return view('livewire.tickets.ticket-create', [
            'projects' => $this->projects,
            'users' => $this->users,
            'priorityOptions' => $this->priorityOptions,
            'typeOptions' => $this->typeOptions,
            'statusOptions' => $this->statusOptions,
            'ticketNumberPreview' => $this->ticketNumberPreview,
        ]);
    }
}
