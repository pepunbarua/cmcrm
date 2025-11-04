<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Vendor extends Model
{
    use LogsActivity;

    protected $fillable = [
        'vendor_name',
        'vendor_type',
        'vendor_type_id',
        'address',
        'city',
        'contact_person',
        'phone',
        'email',
        'commission_rate',
        'status',
        'notes',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
    ];

    public function vendorType()
    {
        return $this->belongsTo(VendorType::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['vendor_name', 'vendor_type', 'status'])
            ->logOnlyDirty();
    }
}
