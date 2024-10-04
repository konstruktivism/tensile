<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\GoogleCalendarController;
use App\Services\GoogleCalendarService;

class JobDailyTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(GoogleCalendarService $googleCalendarService)
    {
        $GoogleCalendarController = new GoogleCalendarController($googleCalendarService);
        $response = $GoogleCalendarController->importEvents();
        \Log::info($response->getData()->message);
    }
}
