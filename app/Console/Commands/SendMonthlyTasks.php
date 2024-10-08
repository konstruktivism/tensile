<?php

namespace App\Console\Commands;

use App\Jobs\JobMailMonthlyTasks;
use Illuminate\Console\Command;

class SendMonthlyTasks extends Command
{
    protected $signature = 'send:monthly-tasks';
    protected $description = 'Send monthly tasks email to projects with notifications enabled';

    public function handle()
    {
        JobMailMonthlyTasks::dispatch();
        $this->info('Monthly tasks email job dispatched successfully.');
    }
}
