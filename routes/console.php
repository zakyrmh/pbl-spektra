<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:move-session')->dailyAt('12:00');
Schedule::command('bookings:cancel-expired')->dailyAt('23:59');
Schedule::command('app:reset-booths-status')->dailyAt('18:00');
