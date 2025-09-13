<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Schedule::command('stakeholders:check-communications')->dailyAt('12:00')->timezone(config('app.timezone'));
//Schedule::command('stakeholders:check-communications')->everyMinute();