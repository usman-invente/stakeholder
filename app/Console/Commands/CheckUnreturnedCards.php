<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitor;
use App\Models\User;
use App\Notifications\UnreturnedCardNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class CheckUnreturnedCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:unreturned-cards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for visitors who have not returned their cards and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for unreturned cards...');

        // Find visitors who have checked out, but haven't returned their cards
        $visitors = Visitor::where('check_out_time', '!=', null)
            ->where('card_returned', false)
            ->get();

        if ($visitors->isEmpty()) {
            $this->info('No unreturned cards found.');
            return 0;
        }

        $this->info('Found ' . $visitors->count() . ' unreturned cards.');

        // Get all receptionists (users with receptionist role)
        $receptionists = User::where('role', 'receptionist')->get();
        
        // If no receptionists found, notify admin instead
        if ($receptionists->isEmpty()) {
            $receptionists = User::where('role', 'admin')->get();
        }

        // Escalation emails should go to specific addresses
        
        $escalationEmails = ['punit@dsmcorridor.com', 'mohamed@dsmcorridor.com'];

        foreach ($visitors as $visitor) {
            // Skip visitors who already had escalation emails sent
            if ($visitor->escalation_email_sent) {
                continue;
            }

            $this->info('Follow up: ' . $visitor->follow_up_count);
            // If visitor has 2 or more follow-ups, send escalation email
            if ($visitor->follow_up_count >= 2 && !$visitor->escalation_email_sent) {
                $this->info('Sending escalation email for visitor: ' . $visitor->full_name);
                
                try {
                    // Create custom notifiable objects for each email
                    $notifiables = collect($escalationEmails)->map(function ($email) {
                        return (object) ['email' => $email, 'name' => 'Management'];
                    });
                    
                    // Send the escalation email to each management email
                    foreach ($escalationEmails as $email) {
                        \Illuminate\Support\Facades\Mail::to($email)
                            ->send(new \App\Mail\UnreturnedCardNotification($visitor, true));
                    }
                    
                    $this->info('Escalation email sent to: ' . implode(', ', $escalationEmails));
                } catch (\Exception $e) {
                    $this->error('Failed to send escalation email: ' . $e->getMessage());
                    \Illuminate\Support\Facades\Log::error('Escalation email failed: ' . $e->getMessage());
                }
                // Update visitor record
                $visitor->escalation_email_sent = true;
                $visitor->save();
            } 
            // Otherwise, check if we should send a follow-up notification
            else {
                // Only send follow-up if last one was more than 24 hours ago or if this is the first one
                $shouldSendFollowUp = !$visitor->last_follow_up;
                
                // If there was a previous follow-up, check if it's been at least 24 hours
                if (!$shouldSendFollowUp && $visitor->last_follow_up) {
                    $hours = DB::raw('TIMESTAMPDIFF(HOUR, last_follow_up, NOW()) as hours_diff');
                    $diff = DB::table('visitors')
                        ->select($hours)
                        ->where('id', $visitor->id)
                        ->first();
                    
                    $shouldSendFollowUp = $diff && $diff->hours_diff >= 24;
                }
                
                if ($shouldSendFollowUp) {
                    $this->info('Sending follow-up notification for visitor: ' . $visitor->full_name);
                    
                    try {
                        // Send the follow-up notification to each receptionist
                        foreach ($receptionists as $receptionist) {
                            \Illuminate\Support\Facades\Mail::to($receptionist->email)
                                ->send(new \App\Mail\UnreturnedCardNotification($visitor, false));
                        }
                        
                        $this->info('Follow-up notification sent to ' . $receptionists->count() . ' receptionists');
                        
                        // Update visitor record
                        $visitor->follow_up_count += 1;
                        $visitor->last_follow_up = now();
                        $visitor->save();
                    } catch (\Exception $e) {
                        $this->error('Failed to send follow-up notification: ' . $e->getMessage());
                        \Illuminate\Support\Facades\Log::error('Follow-up notification failed: ' . $e->getMessage());
                    }
                }
            }
        }

        $this->info('Unreturned card notifications processed successfully.');
        return 0;
    }
}
