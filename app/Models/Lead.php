<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Lead extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'vendor_id', 'user_id', 'client_name', 'client_phone', 'client_email',
        'event_type', 'event_date', 'budget_range', 'package_interest', 
        'notes', 'status',
    ];

    protected $casts = ['event_date' => 'date'];

    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function followUps(): HasMany { return $this->hasMany(FollowUp::class); }
    public function order(): HasOne { return $this->hasOne(Order::class); }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['client_name', 'status'])->logOnlyDirty();
    }
}
