<?php

namespace App\Console\Commands;

use App\Jobs\JobDaysTask;
use Illuminate\Console\Command;

class DaysTask extends Command
{
    protected $signature = 'import:weeks {--weeks=1}';
    protected $description = 'Import tasks based on the specified number of weeks or the current week if not specified';

    public function handle()
    {
        $weeks = $this->argument('weeks');

        JobDaysTask::dispatch($weeks);

        $this->info("Tasks for the last $weeks week(s) have been dispatched to the queue.");

        \Log::info("Tasks for the last $weeks week(s) have been dispatched to the queue.");
    }
}
