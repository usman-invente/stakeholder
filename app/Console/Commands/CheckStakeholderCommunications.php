<?php

namespace App\Console\Commands;

use App\Models\Stakeholder;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StakeholderCommunicationAlert;

class CheckStakeholderCommunications extends Command
{
    protected $signature = 'stakeholders:check-communications';
    protected $description = 'Check for stakeholders without recent communications';

    public function handle()
    {
        Log::info("Starting stakeholder communications check at " . now()->format('Y-m-d H:i:s'));
        $threshold = Setting::getValue('communication_alert_threshold', 30);
        $thresholdDate = now()->subDays($threshold);

        $stakeholders = Stakeholder::whereDoesntHave('communications', function ($query) use ($thresholdDate) {
            $query->where('meeting_date', '>=', $thresholdDate);
        })->get();

        if ($stakeholders->isNotEmpty()) {
            // Send notification to all admin users
            $admins = User::where('role', 'admin')->get();
            Log::info("Found {$admins->count()} admin users");
            
            foreach ($admins as $admin) {
                try {
                    Log::info("Sending to admin: {$admin->name} ({$admin->email})");
                    $admin->notify(new StakeholderCommunicationAlert($stakeholders, $threshold));
                } catch (\Exception $e) {
                    Log::error("Failed to send to admin {$admin->email}: " . $e->getMessage());
                }
            }
            
            // Send notification to regular users as well
            $regularUsers = User::where('role', '!=', 'admin')->get();
            Log::info("Found {$regularUsers->count()} regular users");
            
            foreach ($regularUsers as $user) {
                try {
                    Log::info("Sending to regular user: {$user->name} ({$user->email})");
                    $user->notify(new StakeholderCommunicationAlert($stakeholders, $threshold));
                    Log::info("Successfully sent to {$user->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send to regular user {$user->email}: " . $e->getMessage());
                }
            }

            Log::info("Alerts sent for {$stakeholders->count()} stakeholders to admins and regular users.");
        } else {
           Log::info('No stakeholders without communications found. No alerts needed.');
        }
        Log::info("Finished stakeholder communications check at " . now()->format('Y-m-d H:i:s'));
    }
}
