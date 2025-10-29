<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventNote extends Model
{
    protected $fillable = ['event_id', 'note', 'note_type', 'added_by'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function addedBy(): BelongsTo { return $this->belongsTo(User::class, 'added_by'); }
}
