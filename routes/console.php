<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('stakeholders:check-communications')->dailyAt('12:00')->timezone(config('app.timezone'));
Schedule::command('check:unreturned-cards')->dailyAt('12:00')->timezone(config('app.timezone'));
Schedule::command('visitors:weekly-summary')->weekly()->fridays()->at('17:00')->timezone(config('app.timezone'));
Schedule::command('contracts:check-expiry')->dailyAt('08:00')->timezone(config('app.timezone'));
