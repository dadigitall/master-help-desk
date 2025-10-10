<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FavoriteProject extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'project_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'project_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user that owns the favorite.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that is favorited.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope a query to get favorites for a specific user.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope a query to get favorites for a specific project.
     */
    public function scopeForProject($query, Project $project)
    {
        return $query->where('project_id', $project->id);
    }

    /**
     * Scope a query to order by creation date (newest first).
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to order by creation date (oldest first).
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Check if this is a favorite for a specific user.
     */
    public function isForUser(User $user)
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if this is a favorite for a specific project.
     */
    public function isForProject(Project $project)
    {
        return $this->project_id === $project->id;
    }

    /**
     * Get the project name.
     */
    public function getProjectNameAttribute()
    {
        return $this->project ? $this->project->name : 'Unknown Project';
    }

    /**
     * Get the user name.
     */
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown User';
    }

    /**
     * Get the project color.
     */
    public function getProjectColorAttribute()
    {
        return $this->project ? $this->project->color : '#6B7280';
    }

    /**
     * Get the project prefix.
     */
    public function getProjectPrefixAttribute()
    {
        return $this->project ? $this->project->prefix : '';
    }

    /**
     * Get the project icon HTML.
     */
    public function getProjectIconHtmlAttribute()
    {
        return $this->project ? $this->project->icon->button_svg ?? '' : '';
    }

    /**
     * Get the project URL.
     */
    public function getProjectUrlAttribute()
    {
        return $this->project ? route('projects.show', $this->project->id) : '#';
    }

    /**
     * Get the formatted creation date.
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Get the time ago in human readable format.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create a new favorite project.
     */
    public static function createFavorite(User $user, Project $project)
    {
        // Check if it's already favorited
        if (static::isFavorited($user, $project)) {
            return null;
        }

        return static::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    /**
     * Remove a favorite project.
     */
    public static function removeFavorite(User $user, Project $project)
    {
        return static::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->delete();
    }

    /**
     * Check if a project is favorited by a user.
     */
    public static function isFavorited(User $user, Project $project)
    {
        return static::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->exists();
    }

    /**
     * Get all favorite projects for a user.
     */
    public static function getForUser(User $user, $limit = null)
    {
        $query = static::with('project')
            ->where('user_id', $user->id)
            ->latest();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get all users who favorited a project.
     */
    public static function getUsersForProject(Project $project)
    {
        return static::with('user')
            ->where('project_id', $project->id)
            ->get()
            ->pluck('user');
    }

    /**
     * Get the count of favorites for a project.
     */
    public static function getCountForProject(Project $project)
    {
        return static::where('project_id', $project->id)->count();
    }

    /**
     * Get the count of favorites for a user.
     */
    public static function getCountForUser(User $user)
    {
        return static::where('user_id', $user->id)->count();
    }

    /**
     * Get the most favorited projects.
     */
    public static function getMostFavorited($limit = 10)
    {
        return static::select('project_id')
            ->selectRaw('COUNT(*) as favorite_count')
            ->groupBy('project_id')
            ->orderBy('favorite_count', 'desc')
            ->limit($limit)
            ->with('project')
            ->get()
            ->pluck('project');
    }

    /**
     * Get recently favorited projects for a user.
     */
    public static function getRecentForUser(User $user, $limit = 5)
    {
        return static::with('project')
            ->where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get()
            ->pluck('project');
    }

    /**
     * Toggle favorite status for a user and project.
     */
    public static function toggle(User $user, Project $project)
    {
        if (static::isFavorited($user, $project)) {
            static::removeFavorite($user, $project);
            return false;
        } else {
            static::createFavorite($user, $project);
            return true;
        }
    }

    /**
     * Get favorite projects with tickets count.
     */
    public static function getForUserWithTicketStats(User $user)
    {
        return static::with(['project', 'project.tickets'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($favorite) {
                $project = $favorite->project;
                return [
                    'favorite' => $favorite,
                    'project' => $project,
                    'tickets_count' => $project ? $project->tickets_count : 0,
                    'open_tickets_count' => $project ? $project->open_tickets_count : 0,
                ];
            });
    }

    /**
     * Clean up favorites for deleted projects.
     */
    public static function cleanupOrphaned()
    {
        return static::whereNotExists(function ($query) {
            $query->selectRaw(1)
                ->from('projects')
                ->whereRaw('projects.id = favorite_projects.project_id');
        })->delete();
    }

    /**
     * Clean up favorites for deleted users.
     */
    public static function cleanupOrphanedUsers()
    {
        return static::whereNotExists(function ($query) {
            $query->selectRaw(1)
                ->from('users')
                ->whereRaw('users.id = favorite_projects.user_id');
        })->delete();
    }

    /**
     * Get favorite statistics.
     */
    public static function getStats()
    {
        return [
            'total_favorites' => static::count(),
            'unique_users' => static::distinct('user_id')->count('user_id'),
            'unique_projects' => static::distinct('project_id')->count('project_id'),
            'avg_favorites_per_user' => static::count() / max(static::distinct('user_id')->count('user_id'), 1),
            'avg_favorites_per_project' => static::count() / max(static::distinct('project_id')->count('project_id'), 1),
        ];
    }
}
