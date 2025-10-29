<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deliverable extends Model
{
    protected $fillable = [
        'event_id', 'deliverable_type', 'total_count', 'delivery_method',
        'delivery_link', 'delivery_status', 'delivered_at',
    ];

    protected $casts = ['delivered_at' => 'datetime'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
