<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use App\Mail\ContractExpiryNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckContractExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for contracts nearing expiry and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking contract expiry dates...');

        // Get contracts expiring in 30 days
        $contractsExpiring30Days = Contract::with('department')
            ->whereDate('expiry_date', '=', Carbon::now()->addDays(30)->toDateString())
            ->where('status', '!=', 'expired')
            ->get();

        // Get contracts that expired 40+ days ago without escalation
        $expiredContracts = Contract::with('department')
            ->whereDate('expiry_date', '<', Carbon::now()->subDays(40)->toDateString())
            ->where('status', 'expired')
            ->whereNull('escalation_sent_at') // Add this field to track escalations
            ->get();

        $this->info('Found ' . $contractsExpiring30Days->count() . ' contracts expiring in 30 days');
        $this->info('Found ' . $expiredContracts->count() . ' contracts requiring escalation');

        // Send 30-day expiry warnings to contract owners
        foreach ($contractsExpiring30Days as $contract) {
            try {
                Mail::to($contract->contract_owner_email)
                    ->send(new ContractExpiryNotification($contract, false, 30));
                
                $this->info('Sent 30-day warning to: ' . $contract->contract_owner_email . ' for contract: ' . $contract->contract_id);
                
                // Update contract status if needed
                $contract->updateStatus();
                
            } catch (\Exception $e) {
                $this->error('Failed to send expiry notification for contract ' . $contract->contract_id . ': ' . $e->getMessage());
            }
        }

        // Send escalation emails to management for expired contracts (40+ days)
        $managementEmails = ['management@dsmcorridor.com'];
        
        foreach ($expiredContracts as $contract) {
            try {
                foreach ($managementEmails as $email) {
                    Mail::to($email)
                        ->cc($contract->contract_owner_email) // CC the contract owner
                        ->send(new ContractExpiryNotification($contract, true, abs($contract->days_until_expiry)));
                }
                
                $this->info('Sent escalation email to management for expired contract: ' . $contract->contract_id);
                
                // Mark escalation as sent (you'll need to add this field to the migration)
                $contract->escalation_sent_at = now();
                $contract->save();
                
            } catch (\Exception $e) {
                $this->error('Failed to send escalation for contract ' . $contract->contract_id . ': ' . $e->getMessage());
            }
        }

        // Also check for contracts expiring in 7 days (additional warning)
        $contractsExpiring7Days = Contract::with('department')
            ->whereDate('expiry_date', '=', Carbon::now()->addDays(7)->toDateString())
            ->where('status', '!=', 'expired')
            ->get();

        foreach ($contractsExpiring7Days as $contract) {
            try {
                Mail::to($contract->contract_owner_email)
                    ->send(new ContractExpiryNotification($contract, false, 7));
                
                $this->info('Sent 7-day final warning to: ' . $contract->contract_owner_email . ' for contract: ' . $contract->contract_id);
                
            } catch (\Exception $e) {
                $this->error('Failed to send 7-day warning for contract ' . $contract->contract_id . ': ' . $e->getMessage());
            }
        }

        $this->info('Contract expiry check completed successfully.');
        return 0;
    }
}
