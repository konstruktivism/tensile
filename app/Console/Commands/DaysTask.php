<?php

namespace App\Console\Commands;

use App\Jobs\JobDaysTask;
use Illuminate\Console\Command;

class DaysTask extends Command
{
    protected $signature = 'command:days-task';
    protected $description = 'Import last 30 days';

    public function handle()
    {
        JobDaysTask::dispatch();
        $this->info('Last 30 days task has been dispatched to the queue.');

        \Log::info('Last 30 days task has been dispatched to the queue.');
    }
}
