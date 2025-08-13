<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Schedule::command('stakeholders:check-communications')->dailyAt('00:00')->timezone(config('app.timezone'));
