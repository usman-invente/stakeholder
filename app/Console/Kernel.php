<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CheckStakeholderCommunications::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('stakeholders:check-communications')
                ->daily()
                ->at('00:00')->timezone(config('app.timezone'));
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
