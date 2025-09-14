<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;
    
    /**
     * The timezone for date attributes.
     */
    protected $timezone = 'Africa/Dar_es_Salaam';

    protected $fillable = [
        'full_name',
        'email',
        'card_no',
        'contact_number',
        'host_name',
        'host_email',
        'check_in_time',
        'check_out_time',
        'meeting_id',
        'email_sent',
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
    ];
}
