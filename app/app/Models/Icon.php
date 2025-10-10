<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Icon extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'svg_content',
        'category',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'svg_content', 'category'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the projects that use this icon.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the ticket types that use this icon.
     */
    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by name.
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Get the SVG content with default size classes.
     */
    public function getSizedSvgAttribute($size = 'w-5 h-5')
    {
        $svg = $this->svg_content;
        
        // Add size classes if not present
        if (strpos($svg, 'class=') === false) {
            $svg = str_replace('<svg', "<svg class=\"{$size}\"", $svg);
        } else {
            // Replace existing class with size classes
            $svg = preg_replace('/class="([^"]*)"/', "class=\"{$size} $1\"", $svg);
        }
        
        return $svg;
    }

    /**
     * Get the SVG content for navigation (smaller size).
     */
    public function getNavSvgAttribute()
    {
        return $this->getSizedSvgAttribute('w-4 h-4');
    }

    /**
     * Get the SVG content for buttons (medium size).
     */
    public function getButtonSvgAttribute()
    {
        return $this->getSizedSvgAttribute('w-5 h-5');
    }

    /**
     * Get the SVG content for headers (larger size).
     */
    public function getHeaderSvgAttribute()
    {
        return $this->getSizedSvgAttribute('w-6 h-6');
    }

    /**
     * Get the SVG content as a data URL.
     */
    public function getDataUrlAttribute()
    {
        $svg = $this->svg_content;
        $encoded = rawurlencode($svg);
        return "data:image/svg+xml,{$encoded}";
    }

    /**
     * Get the icon URL (for compatibility with other models).
     */
    public function getUrlAttribute()
    {
        return $this->data_url;
    }

    /**
     * Get the total number of projects using this icon.
     */
    public function getProjectsCountAttribute()
    {
        return $this->projects()->count();
    }

    /**
     * Get the total number of ticket types using this icon.
     */
    public function getTicketTypesCountAttribute()
    {
        return $this->ticketTypes()->count();
    }

    /**
     * Get the total usage count.
     */
    public function getUsageCountAttribute()
    {
        return $this->projects_count + $this->ticket_types_count;
    }

    /**
     * Check if this is a commonly used icon.
     */
    public function isCommon()
    {
        $commonIcons = [
            'home', 'user', 'settings', 'dashboard', 'ticket', 'project',
            'company', 'chat', 'comment', 'bug', 'feature', 'support'
        ];
        
        return in_array(strtolower($this->name), $commonIcons);
    }

    /**
     * Check if this is a technical icon.
     */
    public function isTechnical()
    {
        $technicalCategories = ['development', 'technical', 'code'];
        return in_array($this->category, $technicalCategories);
    }

    /**
     * Check if this is a business icon.
     */
    public function isBusiness()
    {
        $businessCategories = ['business', 'office', 'finance'];
        return in_array($this->category, $businessCategories);
    }

    /**
     * Get all icons by category.
     */
    public static function getByCategory($category)
    {
        return static::byCategory($category)->get();
    }

    /**
     * Get all categories.
     */
    public static function getAllCategories()
    {
        return static::distinct()->pluck('category')->sort()->values();
    }

    /**
     * Get common icons.
     */
    public static function getCommon()
    {
        return static::whereIn('name', [
            'home', 'user', 'settings', 'dashboard', 'ticket', 'project',
            'company', 'chat', 'comment', 'bug', 'feature', 'support'
        ])->get();
    }

    /**
     * Get technical icons.
     */
    public static function getTechnical()
    {
        return static::whereIn('category', ['development', 'technical', 'code'])->get();
    }

    /**
     * Get business icons.
     */
    public static function getBusiness()
    {
        return static::whereIn('category', ['business', 'office', 'finance'])->get();
    }

    /**
     * Get most used icons.
     */
    public static function getMostUsed($limit = 20)
    {
        return static::withCount(['projects', 'ticketTypes'])
            ->orderByRaw('(projects_count + ticket_types_count) DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new icon.
     */
    public static function createIcon(array $data)
    {
        return static::create($data);
    }

    /**
     * Get or create an icon by name.
     */
    public static function getOrCreateByName($name, $svgContent = null, $category = 'general')
    {
        $icon = static::where('name', $name)->first();
        
        if (!$icon) {
            $icon = static::create([
                'name' => $name,
                'svg_content' => $svgContent ?: static::getDefaultSvg($name),
                'category' => $category,
            ]);
        }
        
        return $icon;
    }

    /**
     * Get default SVG content for common icons.
     */
    public static function getDefaultSvg($name)
    {
        $defaultSvgs = [
            'home' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'user' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
            'settings' => '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
        ];
        
        return $defaultSvgs[$name] ?? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>';
    }

    /**
     * Check if the icon can be deleted.
     */
    public function canBeDeleted()
    {
        // Cannot delete if it's being used by projects or ticket types
        return $this->usage_count === 0;
    }

    /**
     * Get icons for dropdown/select options.
     */
    public static function getForSelect($category = null)
    {
        $query = static::query();
        
        if ($category) {
            $query->byCategory($category);
        }
        
        return $query->orderBy('name')->get()->mapWithKeys(function ($icon) {
            return [$icon->id => $icon->name];
        });
    }

    /**
     * Get search results.
     */
    public static function search($term)
    {
        return static::where('name', 'like', "%{$term}%")
            ->orWhere('category', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(50)
            ->get();
    }

    /**
     * Import icons from a JSON file or array.
     */
    public static function importIcons($icons)
    {
        $imported = 0;
        
        foreach ($icons as $iconData) {
            $existing = static::where('name', $iconData['name'])->first();
            
            if (!$existing) {
                static::create([
                    'name' => $iconData['name'],
                    'svg_content' => $iconData['svg_content'],
                    'category' => $iconData['category'] ?? 'general',
                ]);
                $imported++;
            }
        }
        
        return $imported;
    }
}
