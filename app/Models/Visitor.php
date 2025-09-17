<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The timezone for date attributes.
     */
    protected $timezone = 'Africa/Dar_es_Salaam';

    protected $fillable = [
        'full_name',
        'email',
        'card_no',
        'contact_number',
        'coming_from_company',
        'visiting_company',
        'host_name',
        'host_email',
        'check_in_time',
        'check_out_time',
        'meeting_id',
        'email_sent',
        'card_returned',
        'follow_up_count',
        'last_follow_up',
        'escalation_email_sent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_time' => 'datetime:Africa/Dar_es_Salaam',
        'check_out_time' => 'datetime:Africa/Dar_es_Salaam',
        'created_at' => 'datetime:Africa/Dar_es_Salaam',
        'updated_at' => 'datetime:Africa/Dar_es_Salaam',
        'last_follow_up' => 'datetime:Africa/Dar_es_Salaam',
        'deleted_at' => 'datetime:Africa/Dar_es_Salaam',
        'card_returned' => 'boolean',
        'escalation_email_sent' => 'boolean',
    ];
}
