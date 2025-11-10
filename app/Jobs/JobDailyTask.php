<?php

namespace App\Jobs;

use App\Http\Controllers\GoogleCalendarController;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobDailyTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private bool $includeToday = false) {}

    public function handle(GoogleCalendarService $googleCalendarService)
    {
        $googleCalendarController = new GoogleCalendarController($googleCalendarService);
        $response = $googleCalendarController->importEvents($this->includeToday);
        \Log::info($response->getData()->message);
    }
}
