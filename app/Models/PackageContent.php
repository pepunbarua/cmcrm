<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PackageContent extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'unit',
        'description',
        'base_price',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function packageItems(): HasMany
    {
        return $this->hasMany(PackageContentItem::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_content_items')
            ->withPivot([
                'content_name_snapshot',
                'default_qty',
                'default_unit_price',
                'is_mandatory',
                'is_editable',
                'sort_order',
            ])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'unit', 'base_price', 'is_active'])
            ->logOnlyDirty();
    }
}
