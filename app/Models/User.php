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
        'roles',
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
            'roles' => 'array',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        // Check multiple roles first
        if (is_array($this->roles)) {
            return in_array($role, $this->roles);
        }
        
        // Fallback to single role for backward compatibility
        return $this->role === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get user's roles as array
     */
    public function getUserRoles()
    {
        if (is_array($this->roles)) {
            return $this->roles;
        }
        
        // Fallback to single role
        return $this->role ? [$this->role] : [];
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
