<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Comment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'content',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['content', 'is_internal'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the ticket that owns the comment.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include public comments.
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope a query to only include internal comments.
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope a query to filter by ticket.
     */
    public function scopeForTicket($query, $ticketId)
    {
        return $query->where('ticket_id', $ticketId);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the formatted content with line breaks.
     */
    public function getFormattedContentAttribute()
    {
        return nl2br(e($this->content));
    }

    /**
     * Get the excerpt of the content.
     */
    public function getExcerptAttribute($length = 100)
    {
        $content = strip_tags($this->content);
        return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
    }

    /**
     * Check if the comment can be viewed by the given user.
     */
    public function canBeViewedBy(User $user)
    {
        // Internal comments can only be viewed by employees and administrators
        if ($this->is_internal) {
            return $user->hasRole(['Administrator', 'Employee']);
        }

        // Public comments can be viewed by anyone with access to the ticket
        return true;
    }

    /**
     * Check if the comment can be edited by the given user.
     */
    public function canBeEditedBy(User $user)
    {
        // Users can edit their own comments
        if ($this->user_id === $user->id) {
            return true;
        }

        // Administrators can edit any comment
        return $user->hasRole('Administrator');
    }

    /**
     * Check if the comment can be deleted by the given user.
     */
    public function canBeDeletedBy(User $user)
    {
        // Users can delete their own comments
        if ($this->user_id === $user->id) {
            return true;
        }

        // Administrators can delete any comment
        return $user->hasRole('Administrator');
    }

    /**
     * Get the time ago in human readable format.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the creation date in a formatted way.
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Get the user's avatar URL.
     */
    public function getUserAvatarUrlAttribute()
    {
        if ($this->user && $this->user->profile_photo_url) {
            return $this->user->profile_photo_url;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name ?? 'Unknown') . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the user's display name.
     */
    public function getUserDisplayNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown User';
    }

    /**
     * Create a new comment.
     */
    public static function createComment(Ticket $ticket, User $user, string $content, bool $isInternal = false)
    {
        return static::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => $content,
            'is_internal' => $isInternal,
        ]);
    }

    /**
     * Get all attachments for this comment.
     * Note: This would require an attachments table and model.
     */
    public function attachments()
    {
        // This would be implemented when we add file attachments
        // return $this->hasMany(Attachment::class);
        return collect();
    }

    /**
     * Check if the comment has attachments.
     */
    public function hasAttachments()
    {
        return $this->attachments()->count() > 0;
    }
}
