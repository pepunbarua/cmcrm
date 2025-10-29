<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use LogsActivity;

    protected $fillable = [
        'lead_id', 'order_number', 'client_name', 'client_phone', 'client_email',
        'event_type', 'event_date', 'event_venue_name', 'event_address',
        'package_type', 'services_included', 'total_amount', 'advance_paid',
        'balance_due', 'payment_status', 'order_status', 'special_requests', 'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'services_included' => 'array',
        'total_amount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function event(): HasOne { return $this->hasOne(Event::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['order_status', 'payment_status'])->logOnlyDirty();
    }
}
