<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Chat extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_customer',
    ];

    protected $casts = [
        'is_customer' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['message', 'is_customer'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the ticket that owns the chat message.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the chat message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include customer messages.
     */
    public function scopeFromCustomer($query)
    {
        return $query->where('is_customer', true);
    }

    /**
     * Scope a query to only include staff messages.
     */
    public function scopeFromStaff($query)
    {
        return $query->where('is_customer', false);
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
     * Get the formatted message with line breaks.
     */
    public function getFormattedMessageAttribute()
    {
        return nl2br(e($this->message));
    }

    /**
     * Get the excerpt of the message.
     */
    public function getExcerptAttribute($length = 100)
    {
        $message = strip_tags($this->message);
        return strlen($message) > $length ? substr($message, 0, $length) . '...' : $message;
    }

    /**
     * Check if the message is from a customer.
     */
    public function isFromCustomer()
    {
        return $this->is_customer;
    }

    /**
     * Check if the message is from staff.
     */
    public function isFromStaff()
    {
        return !$this->is_customer;
    }

    /**
     * Get the message type label.
     */
    public function getTypeLabelAttribute()
    {
        return $this->is_customer ? 'Customer' : 'Staff';
    }

    /**
     * Get the message type color.
     */
    public function getTypeColorAttribute()
    {
        return $this->is_customer ? 'blue' : 'green';
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
     * Get the sender's display name.
     */
    public function getSenderDisplayNameAttribute()
    {
        if ($this->is_customer) {
            return 'Customer';
        }

        return $this->user ? $this->user->name : 'Unknown Staff';
    }

    /**
     * Get the sender's avatar URL.
     */
    public function getSenderAvatarUrlAttribute()
    {
        if ($this->is_customer) {
            return 'https://ui-avatars.com/api/?name=Customer&color=7F9CF5&background=EBF4FF';
        }

        if ($this->user && $this->user->profile_photo_url) {
            return $this->user->profile_photo_url;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name ?? 'Staff') . '&color=10B981&background=D1FAE5';
    }

    /**
     * Check if the message can be edited by the given user.
     */
    public function canBeEditedBy(User $user)
    {
        // Users can edit their own messages
        if ($this->user_id === $user->id && !$this->is_customer) {
            return true;
        }

        // Administrators can edit any message
        return $user->hasRole('Administrator');
    }

    /**
     * Check if the message can be deleted by the given user.
     */
    public function canBeDeletedBy(User $user)
    {
        // Users can delete their own messages
        if ($this->user_id === $user->id && !$this->is_customer) {
            return true;
        }

        // Administrators can delete any message
        return $user->hasRole('Administrator');
    }

    /**
     * Create a new chat message.
     */
    public static function createMessage(Ticket $ticket, ?User $user, string $message, bool $isCustomer = false)
    {
        return static::create([
            'ticket_id' => $ticket->id,
            'user_id' => $isCustomer ? null : $user?->id,
            'message' => $message,
            'is_customer' => $isCustomer,
        ]);
    }

    /**
     * Create a customer message.
     */
    public static function createCustomerMessage(Ticket $ticket, string $message)
    {
        return static::createMessage($ticket, null, $message, true);
    }

    /**
     * Create a staff message.
     */
    public static function createStaffMessage(Ticket $ticket, User $user, string $message)
    {
        return static::createMessage($ticket, $user, $message, false);
    }

    /**
     * Get all attachments for this chat message.
     * Note: This would require an attachments table and model.
     */
    public function attachments()
    {
        // This would be implemented when we add file attachments
        // return $this->hasMany(Attachment::class);
        return collect();
    }

    /**
     * Check if the message has attachments.
     */
    public function hasAttachments()
    {
        return $this->attachments()->count() > 0;
    }

    /**
     * Get the message class for CSS styling.
     */
    public function getMessageClassAttribute()
    {
        if ($this->is_customer) {
            return 'bg-blue-100 border-blue-200 text-blue-800';
        }

        return 'bg-green-100 border-green-200 text-green-800';
    }

    /**
     * Get the alignment class for the message.
     */
    public function getAlignmentClassAttribute()
    {
        if ($this->is_customer) {
            return 'justify-start';
        }

        return 'justify-end';
    }

    /**
     * Get the bubble class for the message.
     */
    public function getBubbleClassAttribute()
    {
        if ($this->is_customer) {
            return 'rounded-l-lg rounded-br-lg';
        }

        return 'rounded-r-lg rounded-bl-lg';
    }
}
