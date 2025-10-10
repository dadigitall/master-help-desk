<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TicketType extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'icon_id',
        'color',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'icon_id', 'color'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the tickets that have this type.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the icon associated with this ticket type.
     */
    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }

    /**
     * Get the projects that can use this ticket type.
     * Note: This would require a project_ticket_type pivot table
     */
    public function projects()
    {
        // This would be implemented if we need to restrict ticket types by project
        // return $this->belongsToMany(Project::class, 'project_ticket_type');
        return collect();
    }

    /**
     * Scope a query to filter by name.
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->whereHas('icon', function ($query) use ($category) {
            $query->where('category', $category);
        });
    }

    /**
     * Get the ticket type color with default fallback.
     */
    public function getColorAttribute($value)
    {
        return $value ?: '#6B7280';
    }

    /**
     * Get the text color based on the background color.
     */
    public function getTextColorAttribute()
    {
        // Simple contrast calculation
        $color = $this->color;
        $r = hexdec(substr($color, 1, 2));
        $g = hexdec(substr($color, 3, 2));
        $b = hexdec(substr($color, 5, 2));
        
        $luminance = ($r * 0.299 + $g * 0.587 + $b * 0.114) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }

    /**
     * Get the CSS classes for styling.
     */
    public function getCssClassesAttribute()
    {
        return "px-2 py-1 rounded-full text-xs font-medium";
    }

    /**
     * Get the inline style for the type badge.
     */
    public function getInlineStyleAttribute()
    {
        return "background-color: {$this->color}; color: {$this->text_color};";
    }

    /**
     * Get the total number of tickets with this type.
     */
    public function getTicketsCountAttribute()
    {
        return $this->tickets()->count();
    }

    /**
     * Get the percentage of tickets with this type.
     */
    public function getTicketsPercentageAttribute()
    {
        $totalTickets = Ticket::count();
        
        if ($totalTickets === 0) {
            return 0;
        }
        
        return round(($this->tickets_count / $totalTickets) * 100, 2);
    }

    /**
     * Get the icon HTML if an icon is associated.
     */
    public function getIconHtmlAttribute()
    {
        if ($this->icon) {
            return $this->icon->svg_content;
        }
        
        // Default icon SVG
        return '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>';
    }

    /**
     * Get the icon URL if an icon is associated.
     */
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return $this->icon->getUrl();
        }
        
        return null;
    }

    /**
     * Get the category from the associated icon.
     */
    public function getCategoryAttribute()
    {
        return $this->icon ? $this->icon->category : 'general';
    }

    /**
     * Get the short description (first 100 characters).
     */
    public function getShortDescriptionAttribute()
    {
        if (!$this->description) {
            return '';
        }
        
        return strlen($this->description) > 100 
            ? substr($this->description, 0, 100) . '...' 
            : $this->description;
    }

    /**
     * Check if this is a common ticket type.
     */
    public function isCommon()
    {
        $commonTypes = ['Bug', 'Feature Request', 'Question', 'Support'];
        return in_array($this->name, $commonTypes);
    }

    /**
     * Check if this is a technical ticket type.
     */
    public function isTechnical()
    {
        $technicalTypes = ['Bug', 'Technical Issue', 'Performance', 'Security'];
        return in_array($this->name, $technicalTypes);
    }

    /**
     * Check if this is a business ticket type.
     */
    public function isBusiness()
    {
        $businessTypes = ['Feature Request', 'Enhancement', 'Process Improvement'];
        return in_array($this->name, $businessTypes);
    }

    /**
     * Get all ticket types.
     */
    public static function getAll()
    {
        return static::all();
    }

    /**
     * Get common ticket types.
     */
    public static function getCommon()
    {
        return static::whereIn('name', ['Bug', 'Feature Request', 'Question', 'Support'])->get();
    }

    /**
     * Get technical ticket types.
     */
    public static function getTechnical()
    {
        return static::whereIn('name', ['Bug', 'Technical Issue', 'Performance', 'Security'])->get();
    }

    /**
     * Get business ticket types.
     */
    public static function getBusiness()
    {
        return static::whereIn('name', ['Feature Request', 'Enhancement', 'Process Improvement'])->get();
    }

    /**
     * Create a new ticket type.
     */
    public static function createType(array $data)
    {
        return static::create($data);
    }

    /**
     * Check if the ticket type can be deleted.
     */
    public function canBeDeleted()
    {
        // Cannot delete if there are tickets with this type
        return $this->tickets()->count() === 0;
    }

    /**
     * Get the ticket type by name.
     */
    public static function getByName($name)
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get or create a ticket type by name.
     */
    public static function getOrCreateByName($name, $description = null, $color = null)
    {
        $type = static::getByName($name);
        
        if (!$type) {
            $type = static::create([
                'name' => $name,
                'description' => $description,
                'color' => $color ?: '#6B7280',
            ]);
        }
        
        return $type;
    }

    /**
     * Get the most used ticket types.
     */
    public static function getMostUsed($limit = 10)
    {
        return static::withCount('tickets')
            ->orderBy('tickets_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recently used ticket types by a user.
     * Note: This would require tracking ticket creation by user
     */
    public static function getRecentlyUsedByUser(User $user, $limit = 5)
    {
        // This would be implemented when we add user-specific tracking
        return static::getCommon()->take($limit);
    }
}
