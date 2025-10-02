<?php

namespace App\Console\Commands;

use App\Http\Controllers\GoogleCalendarController;
use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;

class ImportDateRange extends Command
{
    protected $signature = 'import:date-range {start_date} {end_date}';
    protected $description = 'Import tasks from Google Calendar for a specific date range (supports past and future dates)';

    public function handle(GoogleCalendarService $googleCalendarService)
    {
        $startDate = $this->argument('start_date');
        $endDate = $this->argument('end_date');

        $this->info("Importing events from {$startDate} to {$endDate}...");

        $controller = new GoogleCalendarController($googleCalendarService);
        $response = $controller->importDateRange($startDate, $endDate);

        $data = $response->getData();

        if (isset($data->error)) {
            $this->error($data->message);
            return 1;
        }

        $this->info($data->message);
        $this->info("Total imported: {$data->imported_count} tasks");

        \Log::info($data->message);

        return 0;
    }
}
