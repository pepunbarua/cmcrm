<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderPackage extends Model
{
    protected $fillable = [
        'order_id',
        'package_id',
        'package_name_snapshot',
        'pricing_mode',
        'base_price',
        'subtotal',
        'discount',
        'adjustment',
        'grand_total',
        'package_snapshot',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'package_snapshot' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(OrderPackageContent::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
