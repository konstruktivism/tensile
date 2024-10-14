<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\JobDailyTask;

class DailyTask extends Command
{
    protected $signature = 'command:daily-task';
    protected $description = 'Import daily tasks';

    public function handle()
    {
        JobDailyTask::dispatch();
        $this->info('Daily task has been dispatched to the queue.');

        \Log::info('Last day task has been dispatched to the queue.');
    }
}
