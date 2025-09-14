<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

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

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];
}
