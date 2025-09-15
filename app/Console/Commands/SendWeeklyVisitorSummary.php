<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitor;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklyVisitorSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitors:weekly-summary {--start=} {--end=} {--test : Run in test mode (will not send email)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a weekly summary of visitors to management';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating weekly visitor summary...');

        // Check if we have custom date parameters
        $startDateStr = $this->option('start');
        $endDateStr = $this->option('end');
        $testMode = $this->option('test');

        // If custom dates provided, use them
        if ($startDateStr && $endDateStr) {
            $startDate = Carbon::parse($startDateStr)->startOfDay();
            $endDate = Carbon::parse($endDateStr)->endOfDay();
        } else {
            // Use default: current date as end date and go back 7 days for start date
            $endDate = Carbon::now()->endOfDay(); // Today at 23:59:59
            $startDate = Carbon::now()->subDays(7)->startOfDay(); // 7 days ago at 00:00:00
        }

        $this->info("Date range: {$startDate->format('Y-m-d H:i:s')} to {$endDate->format('Y-m-d H:i:s')}");

        // Get all visitors for the date range
        $visitors = Visitor::where('check_in_time', '>=', $startDate)
            ->where('check_in_time', '<=', $endDate)
            ->orderBy('check_in_time', 'desc')
            ->get();

        $this->info("Found {$visitors->count()} visitors in the date range");

        // Debug: Show all visitor check-in times
        if ($visitors->count() > 0) {
            $this->info("Sample visitor check-in times:");
            foreach ($visitors->take(5) as $index => $visitor) {
                $this->info("  " . ($index+1) . ". {$visitor->full_name}: {$visitor->check_in_time}");
            }
        } else {
            // If no visitors found, let's check if there are any in the database at all
            $totalVisitors = Visitor::count();
            $recentVisitor = Visitor::orderBy('check_in_time', 'desc')->first();
            
            $this->info("Total visitors in database: {$totalVisitors}");
            if ($recentVisitor) {
                $this->info("Most recent visitor: {$recentVisitor->full_name} at {$recentVisitor->check_in_time}");
            }
        }

        // Count visitors with unreturned cards
        $unreturnedCards = $visitors->where('card_returned', false)->count();

        // Group visitors by host
        $visitorsByHost = $visitors->groupBy('host_name');
        
        // Generate some basic statistics
        $statistics = [
            'total_visitors' => $visitors->count(),
            'unreturned_cards' => $unreturnedCards,
            'unique_hosts' => $visitorsByHost->count(),
            'visitors_by_host' => $visitorsByHost->map->count()->sortDesc()->take(5)->toArray(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        // Send the email
        if ($testMode) {
            $this->info('TEST MODE: Email would be sent to management@dsmcorridor.com');
            $this->table(
                ['Statistic', 'Value'],
                [
                    ['Total Visitors', $statistics['total_visitors']],
                    ['Unreturned Cards', $statistics['unreturned_cards']],
                    ['Unique Hosts', $statistics['unique_hosts']],
                ]
            );
            return 0;
        }
        
        try {
            Mail::to('management@dsmcorridor.com')
                ->send(new \App\Mail\WeeklyVisitorSummary($visitors, $statistics));
            
            $this->info('Weekly visitor summary email sent successfully to management@dsmcorridor.com');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send weekly visitor summary: ' . $e->getMessage());
            return 1;
        }
    }
}