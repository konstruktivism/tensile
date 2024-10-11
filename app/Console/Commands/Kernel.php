<?php

namespace App\Console\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('command:daily-task')->dailyAt('06:00');

        $schedule->command('send:weekly-tasks')->fridays()->at('08:00')->environments(['production']);

        $schedule->command('send:monthly-tasks')->monthlyOn(1, '06:00')->environments(['production']);
    }
}
