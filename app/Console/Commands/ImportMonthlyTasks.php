<?php

namespace App\Console\Commands;

use App\Jobs\JobDaysTask;
use Illuminate\Console\Command;

class ImportMonthlyTasks extends Command
{
    protected $signature = 'import:monthly';
    protected $description = 'Import tasks for the last month';

    public function handle()
    {
        // Import last 30 days (approximately 4-5 weeks)
        $weeks = 5;

        JobDaysTask::dispatch($weeks);

        $this->info("Monthly import dispatched - importing tasks for the last $weeks weeks.");
        \Log::info("Monthly import dispatched - importing tasks for the last $weeks weeks.");
    }
}
