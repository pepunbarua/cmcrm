<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Event extends Model
{
    use LogsActivity;

    protected $fillable = [
        'order_id', 'event_date', 'event_time', 'venue', 'venue_address',
        'photographer_id', 'videographer_id',
        'equipment_checklist', 'special_instructions',
        'status', 'delivery_deadline',
    ];

    protected $casts = [
        'event_date' => 'date',
        'delivery_deadline' => 'date',
    ];

    protected $appends = ['countdown_days'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function photographer(): BelongsTo { return $this->belongsTo(TeamMember::class, 'photographer_id'); }
    public function videographer(): BelongsTo { return $this->belongsTo(TeamMember::class, 'videographer_id'); }
    public function deliverables(): HasMany { return $this->hasMany(Deliverable::class); }
    public function notes(): HasMany { return $this->hasMany(EventNote::class); }

    protected function countdownDays(): Attribute
    {
        return Attribute::make(
            get: fn () => now()->diffInDays($this->delivery_deadline, false)
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['event_status', 'delivery_deadline'])->logOnlyDirty();
    }
}
