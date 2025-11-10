<?php

namespace App\Console\Commands;

use App\Jobs\JobDailyTask;
use Illuminate\Console\Command;

class DailyTask extends Command
{
    protected $signature = 'command:daily-task {--include-today : Include events from the current day}';

    protected $description = 'Import daily tasks';

    public function handle()
    {
        $includeToday = (bool) $this->option('include-today');

        JobDailyTask::dispatch($includeToday);
        $this->info(
            $includeToday
                ? 'Daily task (including today) has been dispatched to the queue.'
                : 'Daily task has been dispatched to the queue.'
        );

        \Log::info(
            $includeToday
                ? 'Today task import has been dispatched to the queue.'
                : 'Last day task has been dispatched to the queue.'
        );
    }
}
