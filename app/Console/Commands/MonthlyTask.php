<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleCalendarService;
use App\Http\Controllers\GoogleCalendarController;

class MonthlyTask extends Command
{
    protected $signature = 'command:monthly-task';
    protected $description = '30 days task';

    protected $googleCalendarService;

    protected $GoogleCalendarController;

    public function __construct(
        GoogleCalendarService $googleCalendarService,
        GoogleCalendarController $GoogleCalendarController)
    {
        parent::__construct();
        $this->googleCalendarService = $googleCalendarService;
        $this->GoogleCalendarController = $GoogleCalendarController;
    }

    public function handle()
    {
        $response = $this->GoogleCalendarController->importEvents30Days();

        $this->info($response->getData()->message);
    }
}
