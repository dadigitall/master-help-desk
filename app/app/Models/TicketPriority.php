<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TicketPriority extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'color',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'color', 'order'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the tickets that have this priority.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Scope a query to order by the order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope a query to get high priority items.
     */
    public function scopeHigh($query)
    {
        return $query->where('order', '<=', 2);
    }

    /**
     * Scope a query to get medium priority items.
     */
    public function scopeMedium($query)
    {
        return $query->where('order', '>', 2)->where('order', '<=', 4);
    }

    /**
     * Scope a query to get low priority items.
     */
    public function scopeLow($query)
    {
        return $query->where('order', '>', 4);
    }

    /**
     * Get the priority color with default fallback.
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
     * Get the inline style for the priority badge.
     */
    public function getInlineStyleAttribute()
    {
        return "background-color: {$this->color}; color: {$this->text_color};";
    }

    /**
     * Get the total number of tickets with this priority.
     */
    public function getTicketsCountAttribute()
    {
        return $this->tickets()->count();
    }

    /**
     * Get the percentage of tickets with this priority.
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
     * Check if this is a high priority.
     */
    public function isHigh()
    {
        return $this->order <= 2;
    }

    /**
     * Check if this is a medium priority.
     */
    public function isMedium()
    {
        return $this->order > 2 && $this->order <= 4;
    }

    /**
     * Check if this is a low priority.
     */
    public function isLow()
    {
        return $this->order > 4;
    }

    /**
     * Get the priority level label.
     */
    public function getLevelAttribute()
    {
        if ($this->isHigh()) {
            return 'High';
        } elseif ($this->isMedium()) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    /**
     * Get the priority icon based on level.
     */
    public function getIconAttribute()
    {
        if ($this->isHigh()) {
            return 'exclamation-triangle';
        } elseif ($this->isMedium()) {
            return 'exclamation-circle';
        } else {
            return 'info-circle';
        }
    }

    /**
     * Get all priorities ordered.
     */
    public static function getAllOrdered()
    {
        return static::ordered()->get();
    }

    /**
     * Get high priorities.
     */
    public static function getHighPriorities()
    {
        return static::high()->ordered()->get();
    }

    /**
     * Get medium priorities.
     */
    public static function getMediumPriorities()
    {
        return static::medium()->ordered()->get();
    }

    /**
     * Get low priorities.
     */
    public static function getLowPriorities()
    {
        return static::low()->ordered()->get();
    }

    /**
     * Create a new priority.
     */
    public static function createPriority(array $data)
    {
        // Set default order if not provided
        if (!isset($data['order'])) {
            $data['order'] = static::getNextOrder();
        }
        
        return static::create($data);
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
     * Reorder priorities.
     */
    public static function reorderPriorities(array $priorityIds)
    {
        foreach ($priorityIds as $index => $priorityId) {
            static::where('id', $priorityId)->update(['order' => $index + 1]);
        }
    }

    /**
     * Check if the priority can be deleted.
     */
    public function canBeDeleted()
    {
        // Cannot delete if there are tickets with this priority
        return $this->tickets()->count() === 0;
    }

    /**
     * Get the default priority (usually the first one).
     */
    public static function getDefault()
    {
        return static::ordered()->first();
    }

    /**
     * Get the priority by name.
     */
    public static function getByName($name)
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get the priority for urgent tickets.
     */
    public static function getUrgent()
    {
        return static::where('name', 'like', '%urgent%')->first() ?: static::ordered()->first();
    }

    /**
     * Get the priority for normal tickets.
     */
    public static function getNormal()
    {
        return static::where('name', 'like', '%normal%')->first() ?: static::ordered()->skip(1)->first();
    }

    /**
     * Get the priority for low tickets.
     */
    public static function getLowPriority()
    {
        return static::where('name', 'like', '%low%')->first() ?: static::ordered()->skip(2)->first();
    }
}
