<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'user_id', 'role_type', 'skill_level', 'availability_status',
        'hourly_rate', 'equipment_owned', 'portfolio_link',
        'is_default_assigned', 'priority_order',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_default_assigned' => 'boolean',
    ];

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    public function assignedEventsAsPhotographer()
    {
        return $this->hasMany(Event::class, 'photographer_id');
    }

    public function assignedEventsAsVideographer()
    {
        return $this->hasMany(Event::class, 'videographer_id');
    }
}
