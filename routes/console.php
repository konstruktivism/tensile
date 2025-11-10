<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('command:daily-task')->dailyAt('06:00');

// Also run the import 10 minutes before the weekly email on Fridays
Schedule::command('command:daily-task --include-today')->fridays()->at('20:50');

Schedule::command('send:weekly-tasks')->fridays()->at('21:00')->environments(['production']);

Schedule::command('send:monthly-tasks')->monthlyOn(1, '06:00')->when(function () {
    return now()->firstOfMonth()->isMonday();
})->environments(['production']);

// Monthly import on the 1st of each month at 05:00
Schedule::command('import:monthly')->monthlyOn(1, '05:00');
