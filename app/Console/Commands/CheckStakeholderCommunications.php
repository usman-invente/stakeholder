<?php

namespace App\Console\Commands;

use App\Models\Stakeholder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StakeholderCommunicationAlert;

class CheckStakeholderCommunications extends Command
{
    protected $signature = 'stakeholders:check-communications';
    protected $description = 'Check for stakeholders without recent communications';

    public function handle()
    {
        $threshold = Setting::getValue('communication_alert_threshold', 30);
        $thresholdDate = now()->subDays($threshold);

        $stakeholders = Stakeholder::whereDoesntHave('communications', function ($query) use ($thresholdDate) {
            $query->where('meeting_date', '>=', $thresholdDate);
        })->get();

        if ($stakeholders->isNotEmpty()) {
            // Send notification to all admin users
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new StakeholderCommunicationAlert($stakeholders, $threshold));
            }

            $this->info("Alerts sent for {$stakeholders->count()} stakeholders.");
        } else {
            $this->info('No alerts needed.');
        }
    }
}
