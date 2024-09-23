<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleCalendarService;
use App\Http\Controllers\GoogleCalendarController;

class DailyTask extends Command
{
    protected $signature = 'command:daily-task';
    protected $description = 'Run daily tasks at 6:00 AM';

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
        $events = $this->googleCalendarService->getEvents();

        $this->GoogleCalendarController->runImport($events);
    }
}
