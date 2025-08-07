<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StakeholderCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'stakeholder_id',
        'user_id',
        'meeting_date',
        'meeting_time',
        'meeting_type',
        'location',
        'attendees',
        'discussion_points',
        'action_items',
        'follow_up_notes',
        'follow_up_date'
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'follow_up_date' => 'date',
    ];

    public function stakeholder(): BelongsTo
    {
        return $this->belongsTo(Stakeholder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Add the many-to-many relationship with users
    public function users()
    {
        return $this->belongsToMany(User::class, 'stakeholder_communication_users', 'stakeholder_communication_id', 'user_id')
            ->withTimestamps();
    }
}
