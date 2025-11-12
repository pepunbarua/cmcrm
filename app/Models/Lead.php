<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Lead extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'vendor_id', 'user_id', 'client_name', 'client_phone', 'client_email',
        'event_type', 'event_date', 'budget_range', 'package_interest', 
        'notes', 'status', 'locked_by', 'locked_at', 'lock_expires_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'locked_at' => 'datetime',
        'lock_expires_at' => 'datetime',
    ];

    // Relationships
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function followUps(): HasMany { return $this->hasMany(FollowUp::class); }
    public function order(): HasOne { return $this->hasOne(Order::class); }
    public function leadActivities(): HasMany { return $this->hasMany(LeadActivity::class); }
    public function lockedBy(): BelongsTo { return $this->belongsTo(User::class, 'locked_by'); }

    // Lead Locking Methods
    public function lock(int $userId, int $minutes = 15): bool
    {
        // Check if lead can be locked
        if ($this->isLocked() && $this->locked_by !== $userId) {
            return false;
        }

        $this->update([
            'locked_by' => $userId,
            'locked_at' => now(),
            'lock_expires_at' => now()->addMinutes($minutes),
        ]);

        return true;
    }

    public function unlock(): bool
    {
        $this->update([
            'locked_by' => null,
            'locked_at' => null,
            'lock_expires_at' => null,
        ]);

        return true;
    }

    public function isLocked(): bool
    {
        if (!$this->locked_by || !$this->lock_expires_at) {
            return false;
        }

        // Check if lock has expired
        if ($this->lock_expires_at->isPast()) {
            $this->unlock();
            return false;
        }

        return true;
    }

    public function canBeLocked(int $userId): bool
    {
        // Lead is not locked
        if (!$this->isLocked()) {
            return true;
        }

        // User already has the lock
        if ($this->locked_by === $userId) {
            return true;
        }

        return false;
    }

    public function isLockedByUser(int $userId): bool
    {
        return $this->isLocked() && $this->locked_by === $userId;
    }

    public function extendLock(int $minutes = 15): bool
    {
        if (!$this->isLocked()) {
            return false;
        }

        $this->update([
            'lock_expires_at' => now()->addMinutes($minutes),
        ]);

        return true;
    }

    // Scopes
    public function scopeUnlocked($query)
    {
        return $query->where(function($q) {
            $q->whereNull('locked_by')
              ->orWhere('lock_expires_at', '<', now());
        });
    }

    public function scopeLockedBy($query, int $userId)
    {
        return $query->where('locked_by', $userId)
                    ->where('lock_expires_at', '>', now());
    }

    // Get latest activity interest level (as status)
    public function getLatestActivityStatusAttribute()
    {
        $latestActivity = $this->leadActivities()
            ->whereNotNull('lead_interest_level')
            ->latest()
            ->first();

        return $latestActivity?->lead_interest_level ?? $this->status;
    }

    // Get latest activity interest level with formatting
    public function getFormattedActivityStatusAttribute()
    {
        $status = $this->latest_activity_status;
        
        $statusColors = [
            'hot' => 'red',
            'warm' => 'orange',
            'cold' => 'blue',
            'not_interested' => 'gray',
            'converted' => 'green',
            'lost' => 'gray',
            // Fallback for lead status
            'new' => 'purple',
            'contacted' => 'blue',
            'follow_up' => 'yellow',
            'qualified' => 'green',
        ];

        return [
            'status' => $status,
            'label' => str_replace('_', ' ', ucfirst($status)),
            'color' => $statusColors[$status] ?? 'gray'
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['client_name', 'status'])->logOnlyDirty();
    }
}
