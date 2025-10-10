<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'email',
        'phone',
        'address',
        'logo',
        'active',
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
            ->logOnly(['name', 'description', 'email', 'phone', 'address', 'active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the users that belong to the company.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withTimestamps()
            ->withPivot('role', 'joined_at');
    }

    /**
     * Get the projects for the company.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the tickets for the company.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get the logo URL attribute.
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        
        return null;
    }

    /**
     * Get the total number of users in the company.
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Get the total number of projects in the company.
     */
    public function getProjectsCountAttribute()
    {
        return $this->projects()->count();
    }

    /**
     * Get the total number of tickets in the company.
     */
    public function getTicketsCountAttribute()
    {
        return $this->tickets()->count();
    }
}
