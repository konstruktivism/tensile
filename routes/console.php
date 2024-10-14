<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('command:daily-task')->everyMinute();

Schedule::command('send:weekly-tasks')->fridays()->at('06:00')->environments(['production']);

Schedule::command('send:monthly-tasks')->monthlyOn(1, '06:00')->environments(['production']);
