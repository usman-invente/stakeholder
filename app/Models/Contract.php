<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class Contract extends Model
{
    protected $fillable = [
        'supplier_name',
        'contract_title',
        'contract_id',
        'start_date',
        'expiry_date',
        'renewal_terms',
        'contract_value',
        'department_id',
        'document_path',
        'status',
        'contract_owner',
        'contract_owner_email',
        'escalation_sent_at',
        'auto_renewal'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'contract_value' => 'decimal:2',
        'escalation_sent_at' => 'datetime',
        'auto_renewal' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getDaysUntilExpiryAttribute()
    {
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiringAttribute()
    {
        return $this->days_until_expiry <= 30 && $this->days_until_expiry > 0;
    }

    public function getIsExpiredAttribute()
    {
        return $this->days_until_expiry < 0;
    }

    public function getFormattedExpiryStatusAttribute()
    {
        $days = $this->days_until_expiry;
        
        if ($days > 0) {
            return "({$days} days remaining)";
        } elseif ($days == 0) {
            return "(Expires today)";
        } else {
            $expiredDays = abs($days);
            $years = floor($expiredDays / 365);
            
            if ($years > 1) {
                $remainingDays = $expiredDays % 365;
                return "(Expired {$years} years and {$remainingDays} days ago)";
            } elseif ($years == 1) {
                $remainingDays = $expiredDays % 365;
                return "(Expired 1 year and {$remainingDays} days ago)";
            } else {
                return "(Expired {$expiredDays} days ago)";
            }
        }
    }

    public function updateStatus()
    {
        if ($this->is_expired) {
            $this->status = 'expired';
        } elseif ($this->is_expiring) {
            $this->status = 'expiring';
        } else {
            $this->status = 'active';
        }
        $this->save();
    }

    public function getFormattedExpiryDateAttribute()
    {
        return $this->expiry_date ? $this->expiry_date->format('d/m/Y') : null;
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : null;
    }

    public function sendAlterationNotification($changes = null, $updatedBy = null)
    {
        try {
            Mail::to($this->contract_owner_email)
                ->send(new \App\Mail\ContractAlterationNotification($this, $changes, $updatedBy));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send contract alteration notification: ' . $e->getMessage());
            return false;
        }
    }
}
