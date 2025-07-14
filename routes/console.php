<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('command:daily-task')->dailyAt('06:00');

// Also run the import 5 minutes before the weekly email on Sundays
Schedule::command('command:daily-task')->sundays()->at('23:54');

Schedule::command('send:weekly-tasks')->sundays()->at('23:59')->environments(['production']);

Schedule::command('send:monthly-tasks')->monthlyOn(1, '06:00')->when(function () {
    return now()->firstOfMonth()->isMonday();
})->environments(['production']);
