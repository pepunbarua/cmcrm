<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id',
        'activity_type',
        'call_outcome',
        'call_duration',
        'call_started_at',
        'call_ended_at',
        'previous_status',
        'new_status',
        'lead_interest_level',
        'notes',
        'discussion_points',
        'follow_up_required',
        'next_follow_up_date',
        'next_follow_up_time',
        'follow_up_notes',
        'actions_taken',
        'performed_by',
        'assigned_to',
        'is_completed',
    ];

    protected $casts = [
        'call_started_at' => 'datetime',
        'call_ended_at' => 'datetime',
        'next_follow_up_date' => 'date',
        'discussion_points' => 'array',
        'actions_taken' => 'array',
        'follow_up_required' => 'boolean',
        'is_completed' => 'boolean',
    ];

    // Relationships
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeCalls($query)
    {
        return $query->where('activity_type', 'call');
    }

    public function scopeFollowUpRequired($query)
    {
        return $query->where('follow_up_required', true);
    }

    public function scopePendingFollowUps($query)
    {
        return $query->where('follow_up_required', true)
                    ->whereNotNull('next_follow_up_date')
                    ->where('is_completed', false);
    }

    public function scopeTodaysFollowUps($query)
    {
        return $query->where('follow_up_required', true)
                    ->whereDate('next_follow_up_date', today())
                    ->where('is_completed', false);
    }

    public function scopeByInterestLevel($query, $level)
    {
        return $query->where('lead_interest_level', $level);
    }

    public function scopePerformedBy($query, $userId)
    {
        return $query->where('performed_by', $userId);
    }
}
