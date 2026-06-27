<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'category',
        'priority',
        'status',
    ];

    // Status Constants
    const STATUS_OPEN = 'open';
    const STATUS_RESPONDED = 'responded';
    const STATUS_CLOSED = 'closed';

    // Priority Constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    // Category Constants
    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_BILLING = 'billing';
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_UPGRADE = 'upgrade';

    /**
     * User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ticket Messages relationship.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Helper to get status badge styling.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'bg-amber-50 text-amber-700 border-amber-100',
            self::STATUS_RESPONDED => 'bg-blue-50 text-blue-700 border-blue-100',
            self::STATUS_CLOSED => 'bg-slate-100 text-slate-600 border-slate-200',
            default => 'bg-slate-50 text-slate-700 border-slate-100',
        };
    }

    /**
     * Helper to get priority badge styling.
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_HIGH => 'bg-rose-50 text-rose-700 border-rose-100',
            self::PRIORITY_MEDIUM => 'bg-indigo-50 text-indigo-700 border-indigo-100',
            self::PRIORITY_LOW => 'bg-slate-50 text-slate-600 border-slate-100',
            default => 'bg-slate-50 text-slate-700 border-slate-100',
        };
    }
}
