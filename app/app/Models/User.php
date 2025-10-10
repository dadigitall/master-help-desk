<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'register_token',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'locale'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the companies created by the user.
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get the projects created by the user.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the tickets created by the user.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the tickets assigned to the user.
     */
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Get the comments created by the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the chat messages created by the user.
     */
    public function chatMessages()
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Get the favorite projects of the user.
     */
    public function favoriteProjects()
    {
        return $this->hasMany(FavoriteProject::class);
    }

    /**
     * Get the projects that the user has favorited.
     */
    public function favoritedProjects()
    {
        return $this->belongsToMany(Project::class, 'favorite_projects')
            ->withTimestamps()
            ->orderBy('favorite_projects.created_at', 'desc');
    }

    /**
     * Check if the user has favorited a specific project.
     */
    public function hasFavoritedProject(Project $project)
    {
        return $this->favoritedProjects()->where('project_id', $project->id)->exists();
    }

    /**
     * Get all tickets related to the user (created or assigned).
     */
    public function allTickets()
    {
        return Ticket::where(function ($query) {
            $query->where('user_id', $this->id)
                  ->orWhere('assigned_to', $this->id);
        });
    }

    /**
     * Get the user's full name with fallback.
     */
    public function getFullNameAttribute()
    {
        return $this->name ?: 'Unknown User';
    }

    /**
     * Get the user's avatar URL with fallback.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->profile_photo_url) {
            return $this->profile_photo_url;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=10B981&background=D1FAE5';
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdministrator()
    {
        return $this->hasRole('Administrator');
    }

    /**
     * Check if the user is a manager.
     */
    public function isManager()
    {
        return $this->hasRole('Manager') || $this->isAdministrator();
    }

    /**
     * Check if the user is an agent.
     */
    public function isAgent()
    {
        return $this->hasRole('Agent') || $this->isManager();
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer()
    {
        return $this->hasRole('Customer');
    }

    /**
     * Get the user's role hierarchy level.
     */
    public function getRoleLevelAttribute()
    {
        if ($this->isAdministrator()) {
            return 4;
        } elseif ($this->isManager()) {
            return 3;
        } elseif ($this->isAgent()) {
            return 2;
        } elseif ($this->isCustomer()) {
            return 1;
        }

        return 0;
    }

    /**
     * Get the user's primary role name.
     */
    public function getPrimaryRoleAttribute()
    {
        $roles = $this->getRoleNames();
        
        if ($roles->contains('Administrator')) {
            return 'Administrator';
        } elseif ($roles->contains('Manager')) {
            return 'Manager';
        } elseif ($roles->contains('Agent')) {
            return 'Agent';
        } elseif ($roles->contains('Customer')) {
            return 'Customer';
        }

        return 'Unknown';
    }

    /**
     * Get the user's timezone.
     */
    public function getTimezoneAttribute()
    {
        return $this->timezone ?? 'UTC';
    }

    /**
     * Get the user's preferred date format.
     */
    public function getDateFormatAttribute()
    {
        return $this->date_format ?? 'd/m/Y';
    }

    /**
     * Get the user's preferred time format.
     */
    public function getTimeFormatAttribute()
    {
        return $this->time_format ?? 'H:i';
    }

    /**
     * Format a datetime according to user's preferences.
     */
    public function formatDateTime($datetime)
    {
        if (!$datetime) {
            return null;
        }

        $format = $this->date_format . ' ' . $this->time_format;
        return $datetime->format($format);
    }

    /**
     * Get the user's language preference.
     */
    public function getLanguageAttribute()
    {
        return $this->locale ?? 'en';
    }

    /**
     * Check if the user can access a specific project.
     */
    public function canAccessProject(Project $project)
    {
        // Administrators can access all projects
        if ($this->isAdministrator()) {
            return true;
        }

        // Users can access projects they created
        if ($project->user_id === $this->id) {
            return true;
        }

        // Add more access control logic here as needed
        // For example: if user is assigned to tickets in the project
        // or if project is public, etc.

        return false;
    }

    /**
     * Check if the user can access a specific ticket.
     */
    public function canAccessTicket(Ticket $ticket)
    {
        // Administrators can access all tickets
        if ($this->isAdministrator()) {
            return true;
        }

        // Users can access tickets they created
        if ($ticket->user_id === $this->id) {
            return true;
        }

        // Users can access tickets assigned to them
        if ($ticket->assigned_to === $this->id) {
            return true;
        }

        // Users can access tickets if they can access the project
        if ($this->canAccessProject($ticket->project)) {
            return true;
        }

        return false;
    }

    /**
     * Get the user's activity summary.
     */
    public function getActivitySummaryAttribute()
    {
        return [
            'tickets_created' => $this->tickets()->count(),
            'tickets_assigned' => $this->assignedTickets()->count(),
            'comments' => $this->comments()->count(),
            'chat_messages' => $this->chatMessages()->count(),
            'projects_created' => $this->projects()->count(),
            'companies_created' => $this->companies()->count(),
        ];
    }
}
