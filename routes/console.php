<?php

use App\Jobs\JobCleanupForecastTasks;
use App\Jobs\JobForecastImport;
use Illuminate\Support\Facades\Schedule;

Schedule::command('command:daily-task')->dailyAt('06:00');

Schedule::job(new JobCleanupForecastTasks, 'default', 'database')->hourlyAt(0);
Schedule::job(new JobForecastImport, 'default', 'database')->hourlyAt(10);

Schedule::command('import:weeks --weeks=1')->saturdays()->at('08:30')->environments(['production']);
Schedule::command('send:weekly-tasks')->saturdays()->at('09:00')->environments(['production']);

Schedule::command('import:monthly')->monthlyOn(1, '05:30')->when(function () {
    return now()->firstOfMonth()->isMonday();
})->environments(['production']);
Schedule::command('send:monthly-tasks')->monthlyOn(1, '06:00')->when(function () {
    return now()->firstOfMonth()->isMonday();
})->environments(['production']);
