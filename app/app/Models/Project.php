<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'prefix',
        'color',
        'icon_id',
        'active',
        'created_by',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'prefix', 'color', 'active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the company that owns the project.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the project.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the icon associated with the project.
     */
    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }

    /**
     * Get the users that belong to the project.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withTimestamps()
            ->withPivot('role', 'joined_at');
    }

    /**
     * Get the tickets for the project.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the favorite projects for the project.
     */
    public function favorites()
    {
        return $this->hasMany(FavoriteProject::class);
    }

    /**
     * Scope a query to only include active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to filter by company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get the next ticket number for this project.
     */
    public function getNextTicketNumber()
    {
        $lastTicket = $this->tickets()
            ->orderBy('ticket_number', 'desc')
            ->first();

        if ($lastTicket) {
            return $lastTicket->ticket_number + 1;
        }

        return 1;
    }

    /**
     * Get the formatted ticket number.
     */
    public function getFormattedTicketNumber($ticketNumber)
    {
        return $this->prefix . str_pad($ticketNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the total number of tickets in the project.
     */
    public function getTicketsCountAttribute()
    {
        return $this->tickets()->count();
    }

    /**
     * Get the number of open tickets in the project.
     */
    public function getOpenTicketsCountAttribute()
    {
        return $this->tickets()
            ->whereHas('status', function ($query) {
                $query->where('is_closed', false);
            })
            ->count();
    }

    /**
     * Get the total number of users in the project.
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Check if the project is favorited by a specific user.
     */
    public function isFavoritedBy(User $user)
    {
        return $this->favorites()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get the project color with default fallback.
     */
    public function getColorAttribute($value)
    {
        return $value ?: '#3B82F6';
    }
}
