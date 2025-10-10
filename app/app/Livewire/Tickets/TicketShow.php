<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketShow extends Component
{
    use AuthorizesRequests;

    public $showModal = false;
    public $ticket;
    public $ticketId;
    public $newComment = '';
    public $newChatMessage = '';
    public $activeTab = 'details'; // details, comments, chat, history

    protected $listeners = [
        'openViewTicketModal' => 'openModal',
        'refreshTicketShow' => '$refresh',
    ];

    public function openModal($ticketId)
    {
        $this->authorize('tickets.view');

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
        $this->ticket = Ticket::with([
            'project',
            'user',
            'assignedUser',
            'comments' => function($query) {
                $query->with('user')->latest();
            },
            'chats' => function($query) {
                $query->with('user')->latest();
            }
        ])->findOrFail($this->ticketId);

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
    }

    public function addComment()
    {
        $this->authorize('tickets.comment');

        $this->validate([
            'newComment' => 'required|string|min:3|max:2000',
        ], [
            'newComment.required' => 'Comment cannot be empty.',
            'newComment.min' => 'Comment must be at least 3 characters.',
            'newComment.max' => 'Comment may not be greater than 2000 characters.',
        ]);

        try {
            Comment::create([
                'content' => $this->newComment,
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
            ]);

            $this->newComment = '';
            $this->loadTicket();
            $this->dispatch('refreshTicketShow');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Comment added successfully'
            ]);
        } catch (\Exception $e) {
            $this->addError('comment', 'Failed to add comment. Please try again.');
        }
    }

    public function addChatMessage()
    {
        $this->authorize('tickets.chat');

        $this->validate([
            'newChatMessage' => 'required|string|min:1|max:500',
        ], [
            'newChatMessage.required' => 'Message cannot be empty.',
            'newChatMessage.max' => 'Message may not be greater than 500 characters.',
        ]);

        try {
            Chat::create([
                'message' => $this->newChatMessage,
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
            ]);

            $this->newChatMessage = '';
            $this->loadTicket();
            $this->dispatch('refreshTicketShow');
        } catch (\Exception $e) {
            $this->addError('chat', 'Failed to send message. Please try again.');
        }
    }

    public function updateTicketStatus($status)
    {
        $this->authorize('tickets.edit');

        try {
            $this->ticket->update(['status' => $status]);
            $this->loadTicket();
            $this->dispatch('refreshTicketShow');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Ticket status updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update status'
            ]);
        }
    }

    public function updateTicketPriority($priority)
    {
        $this->authorize('tickets.edit');

        try {
            $this->ticket->update(['priority' => $priority]);
            $this->loadTicket();
            $this->dispatch('refreshTicketShow');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Ticket priority updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update priority'
            ]);
        }
    }

    public function assignTicket($userId)
    {
        $this->authorize('tickets.edit');

        try {
            $this->ticket->update(['assigned_to' => $userId ?: null]);
            $this->loadTicket();
            $this->dispatch('refreshTicketShow');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Ticket assigned successfully'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to assign ticket'
            ]);
        }
    }

    public function deleteComment($commentId)
    {
        $this->authorize('tickets.delete');

        try {
            $comment = Comment::findOrFail($commentId);
            
            // Check if user can delete this comment
            if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Unauthorized action'
                ]);
                return;
            }

            $comment->delete();
            $this->loadTicket();
            $this->dispatch('refreshTicketShow');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to delete comment'
            ]);
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
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

    public function getUsersProperty()
    {
        if (!$this->ticket) {
            return collect();
        }

        return User::whereHas('companies', function($query) use ($this->ticket) {
            $query->where('companies.id', $this->ticket->project->company_id);
        })->orderBy('name')->get();
    }

    public function getCanEditProperty()
    {
        return auth()->user()->can('tickets.edit');
    }

    public function getCanCommentProperty()
    {
        return auth()->user()->can('tickets.comment');
    }

    public function getCanChatProperty()
    {
        return auth()->user()->can('tickets.chat');
    }

    public function getCanDeleteProperty()
    {
        return auth()->user()->can('tickets.delete');
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
            'comments_count' => $this->ticket->comments->count(),
            'chat_messages_count' => $this->ticket->chats->count(),
            'time_ago' => $this->ticket->created_at->diffForHumans(),
        ];
    }

    public function render()
    {
        return view('livewire.tickets.ticket-show', [
            'priorityOptions' => $this->priorityOptions,
            'statusOptions' => $this->statusOptions,
            'users' => $this->users,
            'ticketStats' => $this->ticketStats,
        ]);
    }
}
