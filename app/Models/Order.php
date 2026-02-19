<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use LogsActivity;

    protected $fillable = [
        'lead_id',
        'customer_id',
        'package_id',
        'order_number',
        'client_name',
        'client_phone',
        'client_email',
        'event_type',
        'event_date',
        'event_end_date',
        'time_duration',
        'event_venue_name',
        'location',
        'event_address',
        'bride_name',
        'groom_name',
        'requirements',
        'photographer_count',
        'videographer_count',
        'outdoor_shoot',
        'package_type',
        'package_name',
        'package_details',
        'services_included',
        'total_amount',
        'discount_amount',
        'advance_paid',
        'balance_due',
        'payment_status',
        'order_status',
        'special_requests',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_end_date' => 'date',
        'services_included' => 'array',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'outdoor_shoot' => 'boolean',
    ];

    protected $appends = [
        'client_display_name',
        'client_display_phone',
        'client_display_email',
        'package_display_name',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function orderPackages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function primaryOrderPackage(): HasOne
    {
        return $this->hasOne(OrderPackage::class)->latestOfMany();
    }

    protected function clientDisplayName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->client_name
                ?: $this->customer?->full_name
                ?: $this->lead?->client_name
                ?: 'N/A'
        );
    }

    protected function clientDisplayPhone(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->client_phone
                ?: $this->customer?->phone
                ?: $this->lead?->client_phone
                ?: 'N/A'
        );
    }

    protected function clientDisplayEmail(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->client_email
                ?: $this->customer?->email
                ?: $this->lead?->client_email
                ?: null
        );
    }

    protected function packageDisplayName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->package_name
                ?: $this->primaryOrderPackage?->package_name_snapshot
                ?: 'Custom Package'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['order_status', 'payment_status'])->logOnlyDirty();
    }
}
