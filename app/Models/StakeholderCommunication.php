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
        'meeting_date' => 'datetime',
        'follow_up_date' => 'datetime',
        'meeting_time' => 'datetime',
    ];

    public function getMeetingDateTimeAttribute()
    {
        if ($this->meeting_date && $this->meeting_time) {
            return \Carbon\Carbon::parse($this->meeting_date->format('Y-m-d') . ' ' . $this->meeting_time->format('H:i:s'))
                ->setTimezone('Africa/Dar_es_Salaam');
        }
        return null;
    }

    public function getFormattedMeetingDateAttribute()
    {
        return $this->meeting_date ? $this->meeting_date->setTimezone('Africa/Dar_es_Salaam')->format('M d, Y') : null;
    }

    public function getFormattedMeetingTimeAttribute()
    {
        return $this->meeting_time ? $this->meeting_time->setTimezone('Africa/Dar_es_Salaam')->format('h:i A') : null;
    }

    public function getFormattedFollowUpDateAttribute()
    {
        return $this->follow_up_date ? $this->follow_up_date->setTimezone('Africa/Dar_es_Salaam')->format('M d, Y') : null;
    }

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
