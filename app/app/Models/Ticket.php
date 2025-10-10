<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Ticket extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'project_id',
        'ticket_number',
        'title',
        'description',
        'status_id',
        'priority_id',
        'type_id',
        'assigned_to',
        'created_by',
        'company_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'status_id', 'priority_id', 'type_id', 'assigned_to'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the project that owns the ticket.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the company that owns the ticket.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the status of the ticket.
     */
    public function status()
    {
        return $this->belongsTo(TicketStatus::class);
    }

    /**
     * Get the priority of the ticket.
     */
    public function priority()
    {
        return $this->belongsTo(TicketPriority::class);
    }

    /**
     * Get the type of the ticket.
     */
    public function type()
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * Get the user who created the ticket.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to the ticket.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the users that belong to the ticket.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'ticket_user')
            ->withTimestamps()
            ->withPivot('role', 'added_at');
    }

    /**
     * Get the comments for the ticket.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the chat messages for the ticket.
     */
    public function chatMessages()
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Scope a query to filter by project.
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope a query to filter by company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopeWithPriority($query, $priorityId)
    {
        return $query->where('priority_id', $priorityId);
    }

    /**
     * Scope a query to filter by assigned user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope a query to filter by creator.
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope a query to only include open tickets.
     */
    public function scopeOpen($query)
    {
        return $query->whereHas('status', function ($query) {
            $query->where('is_closed', false);
        });
    }

    /**
     * Scope a query to only include closed tickets.
     */
    public function scopeClosed($query)
    {
        return $query->whereHas('status', function ($query) {
            $query->where('is_closed', true);
        });
    }

    /**
     * Get the formatted ticket number.
     */
    public function getFormattedTicketNumberAttribute()
    {
        if ($this->project) {
            return $this->project->getFormattedTicketNumber($this->ticket_number);
        }
        
        return '#' . str_pad($this->ticket_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the ticket URL.
     */
    public function getUrlAttribute()
    {
        return route('tickets.show', $this->id);
    }

    /**
     * Check if the ticket is closed.
     */
    public function isClosed()
    {
        return $this->status && $this->status->is_closed;
    }

    /**
     * Check if the ticket is open.
     */
    public function isOpen()
    {
        return !$this->isClosed();
    }

    /**
     * Get the latest comment.
     */
    public function getLatestCommentAttribute()
    {
        return $this->comments()->latest()->first();
    }

    /**
     * Get the latest chat message.
     */
    public function getLatestChatMessageAttribute()
    {
        return $this->chatMessages()->latest()->first();
    }

    /**
     * Get the total number of comments.
     */
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    /**
     * Get the total number of chat messages.
     */
    public function getChatMessagesCountAttribute()
    {
        return $this->chatMessages()->count();
    }

    /**
     * Assign the ticket to a user.
     */
    public function assignTo(User $user)
    {
        $this->assigned_to = $user->id;
        $this->save();
        
        // Add user to ticket users if not already there
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id, ['role' => 'assigned', 'added_at' => now()]);
        }
    }

    /**
     * Unassign the ticket.
     */
    public function unassign()
    {
        $this->assigned_to = null;
        $this->save();
    }

    /**
     * Change the ticket status.
     */
    public function changeStatus($statusId)
    {
        $this->status_id = $statusId;
        $this->save();
    }

    /**
     * Add a user to the ticket.
     */
    public function addUser(User $user, $role = 'collaborator')
    {
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id, ['role' => $role, 'added_at' => now()]);
        }
    }

    /**
     * Remove a user from the ticket.
     */
    public function removeUser(User $user)
    {
        $this->users()->detach($user->id);
        
        // If user was assigned, unassign
        if ($this->assigned_to == $user->id) {
            $this->unassign();
        }
    }
}
