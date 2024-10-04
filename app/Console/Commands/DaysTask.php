<?php

namespace App\Console\Commands;

use App\Jobs\JobDaysTask;
use Illuminate\Console\Command;
use App\Jobs\JobDailyTask;

class DaysTask extends Command
{
    protected $signature = 'command:days-task';
    protected $description = 'Import last 30 days';

    public function handle()
    {
        JobDaysTask::dispatch();
        $this->info('Last 30 days task has been dispatched to the queue.');
    }
}
