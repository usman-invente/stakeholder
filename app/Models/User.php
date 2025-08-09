<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Add the many-to-many relationship with communications
    public function communications()
    {
        return $this->belongsToMany(StakeholderCommunication::class, 'stakeholder_communication_users', 'user_id', 'stakeholder_communication_id')
            ->withTimestamps();
    }
    
    // Add relationship to get assigned stakeholders
    public function stakeholders()
    {
        return $this->belongsToMany(Stakeholder::class, 'stakeholder_users', 'user_id', 'stakeholder_id')
            ->withTimestamps();
    }
}
