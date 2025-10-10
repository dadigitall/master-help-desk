<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TicketStatus extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'color',
        'order',
        'is_default',
        'is_closed',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_closed' => 'boolean',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'color', 'order', 'is_default', 'is_closed'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the tickets that have this status.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Scope a query to only include default statuses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include closed statuses.
     */
    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    /**
     * Scope a query to only include open statuses.
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    /**
     * Scope a query to order by the order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the status color with default fallback.
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
     * Get the inline style for the status badge.
     */
    public function getInlineStyleAttribute()
    {
        return "background-color: {$this->color}; color: {$this->text_color};";
    }

    /**
     * Get the total number of tickets with this status.
     */
    public function getTicketsCountAttribute()
    {
        return $this->tickets()->count();
    }

    /**
     * Get the percentage of tickets with this status.
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
     * Check if this is the default status.
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * Check if this is a closed status.
     */
    public function isClosed()
    {
        return $this->is_closed;
    }

    /**
     * Check if this is an open status.
     */
    public function isOpen()
    {
        return !$this->is_closed;
    }

    /**
     * Set this status as the default status.
     */
    public function setAsDefault()
    {
        // Remove default from all other statuses
        static::where('id', '!=', $this->id)->update(['is_default' => false]);
        
        // Set this as default
        $this->is_default = true;
        $this->save();
    }

    /**
     * Get the default status.
     */
    public static function getDefault()
    {
        return static::default()->first();
    }

    /**
     * Get all open statuses ordered.
     */
    public static function getOpenOrdered()
    {
        return static::open()->ordered()->get();
    }

    /**
     * Get all closed statuses ordered.
     */
    public static function getClosedOrdered()
    {
        return static::closed()->ordered()->get();
    }

    /**
     * Get all statuses ordered.
     */
    public static function getAllOrdered()
    {
        return static::ordered()->get();
    }

    /**
     * Create a new status and optionally set it as default.
     */
    public static function createStatus(array $data, bool $isDefault = false)
    {
        $status = static::create($data);
        
        if ($isDefault) {
            $status->setAsDefault();
        }
        
        return $status;
    }

    /**
     * Get the next order value.
     */
    public static function getNextOrder()
    {
        $maxOrder = static::max('order');
        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * Reorder statuses.
     */
    public static function reorderStatuses(array $statusIds)
    {
        foreach ($statusIds as $index => $statusId) {
            static::where('id', $statusId)->update(['order' => $index + 1]);
        }
    }

    /**
     * Check if the status can be deleted.
     */
    public function canBeDeleted()
    {
        // Cannot delete if it's the default status
        if ($this->is_default) {
            return false;
        }
        
        // Cannot delete if there are tickets with this status
        if ($this->tickets()->count() > 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the status icon based on whether it's open or closed.
     */
    public function getIconAttribute()
    {
        if ($this->is_closed) {
            return 'check-circle';
        }
        
        return 'clock';
    }
}
