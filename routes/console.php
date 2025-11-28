<?php

use App\Jobs\JobCleanupForecastTasks;
use App\Jobs\JobForecastImport;
use Illuminate\Support\Facades\Schedule;

Schedule::command('command:daily-task --include-today')->hourlyAt(5);

Schedule::job(new JobCleanupForecastTasks)->hourlyAt(0);
Schedule::job(new JobForecastImport)->hourlyAt(10);

Schedule::command('send:weekly-tasks')->fridays()->at('21:00')->environments(['production']);

Schedule::command('send:monthly-tasks')->monthlyOn(1, '06:00')->when(function () {
    return now()->firstOfMonth()->isMonday();
})->environments(['production']);
