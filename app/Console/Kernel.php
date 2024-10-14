<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('command:daily-task')->everyMinute();
//
//        $schedule->command('send:weekly-tasks')->fridays()->at('08:00')->environments(['production']);
//
//        $schedule->command('send:monthly-tasks')->monthlyOn(1, '06:00')->environments(['production']);
    }
}
