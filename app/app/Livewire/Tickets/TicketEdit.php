<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketEdit extends Component
{
    use AuthorizesRequests;

    public $showModal = false;
    public $ticket;
    public $ticketId;
    public $title = '';
    public $description = '';
    public $project_id = '';
    public $priority = 'medium';
    public $type = 'bug';
    public $assigned_to = '';
    public $status = 'open';
    public $originalTicketNumber = '';

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
        'openEditTicketModal' => 'openModal',
    ];

    public function openModal($ticketId)
    {
        $this->authorize('tickets.edit');

        $this->ticketId = $ticketId;
        $this->loadTicket();
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->resetErrorBag();
    }

    public function loadTicket()
    {
        $this->ticket = Ticket::findOrFail($this->ticketId);
        
        // Check if user has access to this ticket
        $userProjects = auth()->user()->companies()->with('projects')->get()
            ->pluck('projects')->flatten()->pluck('id')->toArray();
        
        if (!in_array($this->ticket->project_id, $userProjects)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Unauthorized action'
            ]);
            $this->closeModal();
            return;
        }

        $this->title = $this->ticket->title;
        $this->description = $this->ticket->description;
        $this->project_id = $this->ticket->project_id;
        $this->priority = $this->ticket->priority;
        $this->type = $this->ticket->type;
        $this->assigned_to = $this->ticket->assigned_to;
        $this->status = $this->ticket->status;
        $this->originalTicketNumber = $this->ticket->ticket_number;
    }

    public function save()
    {
        $this->authorize('tickets.edit');

        $validated = $this->validate();

        try {
            // Check if user has access to this ticket
            $userProjects = auth()->user()->companies()->with('projects')->get()
                ->pluck('projects')->flatten()->pluck('id')->toArray();
            
            if (!in_array($this->ticket->project_id, $userProjects)) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Unauthorized action'
                ]);
                return;
            }

            // Check if project changed and update ticket number if needed
            if ($this->project_id != $this->ticket->project_id) {
                $newTicketNumber = $this->generateTicketNumber($validated['project_id']);
                $validated['ticket_number'] = $newTicketNumber;
            }

            $this->ticket->update($validated);

            $this->dispatch('ticketUpdated');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Ticket updated successfully!'
            ]);

            $this->closeModal();
        } catch (\Exception $e) {
            $this->addError('update', 'Failed to update ticket. Please try again.');
        }
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
            return $this->originalTicketNumber;
        }

        if ($this->project_id == $this->ticket->project_id) {
            return $this->originalTicketNumber;
        }

        return $this->generateTicketNumber($this->project_id);
    }

    public function getTicketStatsProperty()
    {
        if (!$this->ticket) {
            return collect();
        }

        return [
            'created_at' => $this->ticket->created_at->format('M d, Y H:i'),
            'updated_at' => $this->ticket->updated_at->format('M d, Y H:i'),
            'created_by' => $this->ticket->user->name,
            'comments_count' => $this->ticket->comments()->count(),
            'chat_messages_count' => $this->ticket->chats()->count(),
        ];
    }

    public function render()
    {
        return view('livewire.tickets.ticket-edit', [
            'projects' => $this->projects,
            'users' => $this->users,
            'priorityOptions' => $this->priorityOptions,
            'typeOptions' => $this->typeOptions,
            'statusOptions' => $this->statusOptions,
            'ticketNumberPreview' => $this->ticketNumberPreview,
            'ticketStats' => $this->ticketStats,
        ]);
    }
}
