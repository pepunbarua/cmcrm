<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageContentItem extends Model
{
    protected $fillable = [
        'package_id',
        'package_content_id',
        'content_name_snapshot',
        'default_qty',
        'default_unit_price',
        'is_mandatory',
        'is_editable',
        'sort_order',
    ];

    protected $casts = [
        'default_qty' => 'decimal:2',
        'default_unit_price' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_editable' => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function packageContent(): BelongsTo
    {
        return $this->belongsTo(PackageContent::class);
    }
}
