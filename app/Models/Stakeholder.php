<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stakeholder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'organization',
        'position',
        'address',
        'type',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function communications()
    {
        return $this->hasMany(StakeholderCommunication::class);
    }
    
    // Relationship with users who are assigned to this stakeholder
    public function users()
    {
        return $this->belongsToMany(User::class, 'stakeholder_users', 'stakeholder_id', 'user_id')
            ->withTimestamps();
    }
}
