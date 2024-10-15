<?php

namespace App\Console\Commands;

use App\Jobs\JobMailWeeklyTasks;
use Illuminate\Console\Command;

class SendWeeklyTasks extends Command
{
    protected $signature = 'send:weekly-tasks {--week= : The week number to send tasks for}';
    protected $description = 'Send weekly tasks email to projects with notifications enabled';

    public function handle()
    {
        $weekNumber = $this->option('week') ?? now()->weekOfYear;
        JobMailWeeklyTasks::dispatch($weekNumber);
        $this->info('Weekly tasks email job dispatched successfully.');
    }
}
