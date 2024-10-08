<?php

namespace App\Console\Commands;

use App\Jobs\JobMailWeeklyTasks;
use Illuminate\Console\Command;

class SendWeeklyTasks extends Command
{
    protected $signature = 'send:weekly-tasks';
    protected $description = 'Send weekly tasks email to projects with notifications enabled';

    public function handle()
    {
        JobMailWeeklyTasks::dispatch();
        $this->info('Weekly tasks email job dispatched successfully.');
    }
}
