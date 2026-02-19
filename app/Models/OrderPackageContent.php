<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPackageContent extends Model
{
    protected $fillable = [
        'order_package_id',
        'package_content_id',
        'content_name_snapshot',
        'qty',
        'unit_price',
        'line_total',
        'is_mandatory',
        'is_editable',
        'sort_order',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_editable' => 'boolean',
    ];

    public function orderPackage(): BelongsTo
    {
        return $this->belongsTo(OrderPackage::class);
    }

    public function packageContent(): BelongsTo
    {
        return $this->belongsTo(PackageContent::class);
    }
}
