<?php

namespace App\Jobs;

use App\Models\ForecastTask;
use App\Models\Project;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JobForecastImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(GoogleCalendarService $googleCalendarService): void
    {
        // Soft delete old forecasts before importing new ones (all tasks before today)
        $today = Carbon::now()->startOfDay();
        $deletedCount = ForecastTask::where('scheduled_at', '<', $today)
            ->whereNull('deleted_at')
            ->delete();

        if ($deletedCount > 0) {
            Log::info("Soft deleted {$deletedCount} old forecast tasks before import.");
        }

        $currentWeekStart = Carbon::now()->startOfWeek();

        // Import from start of current week to catch events for newly added projects
        // This ensures events for projects added later are still imported
        // (events from before current week are filtered out below)
        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->addWeeks(12)->format('Y-m-d');
        $events = $googleCalendarService->getEventsByDateRange($startDate, $endDate, 1000);

        $importedCount = 0;
        $now = Carbon::now();

        foreach ($events as $event) {
            $start = $event->start->dateTime ?? $event->start->date;
            $end = $event->end->dateTime ?? $event->end->date;

            // Import events from current week onwards (to catch newly added projects)
            // Skip events that ended before the current week started
            $eventEnd = $end ? Carbon::parse($end) : null;
            $eventStart = Carbon::parse($start);

            // Skip events that ended before current week (already in the past)
            if ($eventEnd && $eventEnd->isBefore($currentWeekStart)) {
                continue;
            }

            // Normalize to day to avoid duplicates for the same calendar entry within a day
            $scheduledAt = Carbon::parse($start)->startOfDay();
            $projectCode = substr(preg_replace('/[^A-Z]/', '', $event->getSummary()), 0, 3);

            $project = Project::where('project_code', $projectCode)->first();

            if ($project) {
                $name = preg_replace('/[^a-zA-Z0-9\s.]/', '', substr($event->getSummary(), 4));
                // Filter out all text starting with a /
                $name = preg_replace('/\/\S*/', '', $name);

                $created = ForecastTask::updateOrCreate(
                    [
                        'icalUID' => $event->iCalUID,
                        'scheduled_at' => $scheduledAt,
                    ],
                    [
                        'name' => $name,
                        'description' => $event->getDescription() ?? '',
                        'project_id' => $project->id,
                        'minutes' => isset($event->start->dateTime) && isset($event->end->dateTime) ? ceil((strtotime($event->end->dateTime) - strtotime($event->start->dateTime)) / 60) : 0,
                        'is_service' => str_contains($event->getSummary(), '🆓') ? 1 : 0,
                    ]
                );

                if ($created->wasRecentlyCreated) {
                    $importedCount++;
                }
            }
        }

        Log::info("Forecast import completed. Imported {$importedCount} new forecast tasks.");
    }
}
