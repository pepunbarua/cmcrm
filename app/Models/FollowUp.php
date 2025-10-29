<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    protected $fillable = [
        'lead_id', 'follow_up_date', 'follow_up_time', 'call_duration',
        'call_status', 'notes', 'next_follow_up_date', 'contacted_by', 'status',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'next_follow_up_date' => 'date',
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function contactedBy(): BelongsTo { return $this->belongsTo(User::class, 'contacted_by'); }
}
