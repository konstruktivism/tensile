<?php

namespace App\Console\Commands;

use App\Jobs\JobMailWeeklyTasks;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SendWeeklyTasks extends Command
{
    protected $signature = 'send:weekly-tasks {--weeks=* : The week numbers to send tasks for}';
    protected $description = 'Send weekly tasks email to projects with notifications enabled';

    public function handle()
    {
        $weekNumbers = $this->option('weeks');
        if (empty($weekNumbers)) {
            $weekNumbers = [now()->weekOfYear];
        } else {
            $weekNumbers = array_map('intval', Arr::flatten($weekNumbers));
        }

        JobMailWeeklyTasks::dispatch($weekNumbers)->handle();

        $this->info('Weekly tasks email job dispatched successfully for weeks: ' . implode(', ', $weekNumbers));
    }
}
