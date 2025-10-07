<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    protected $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    public function importEvents(): \Illuminate\Http\JsonResponse
    {
        $events = $this->googleCalendarService->getEvents();

        $this->runImport($events);

        return response()->json(['message' => 'Events imported of yesterday.']);
    }

    public function importWeeks($weeks): \Illuminate\Http\JsonResponse
    {
        $events = $this->googleCalendarService->getEvents(500, $weeks * 7);

        $this->runImport($events);

        return response()->json(['message' => 'Events imported of the last month.']);
    }

    public function importLastMonth(): \Illuminate\Http\JsonResponse
    {
        $events = $this->googleCalendarService->getEvents(1000, 30); // Last 30 days

        $importedCount = $this->runImportWithCount($events);

        return response()->json([
            'message' => "Successfully imported {$importedCount} tasks from the last month.",
            'imported_count' => $importedCount
        ]);
    }

    public function importWeeksAdmin($weeks): \Illuminate\Http\JsonResponse
    {
        $events = $this->googleCalendarService->getEvents(1000, $weeks * 7);

        $importedCount = $this->runImportWithCount($events);

        return response()->json([
            'message' => "Successfully imported {$importedCount} tasks from the last {$weeks} weeks.",
            'imported_count' => $importedCount,
            'weeks' => $weeks
        ]);
    }

    public function importDateRange(string $startDate, string $endDate): \Illuminate\Http\JsonResponse
    {
        try {
            $events = $this->googleCalendarService->getEventsByDateRange($startDate, $endDate);

            $importedCount = $this->runImportWithCount($events);

            return response()->json([
                'message' => "Successfully imported {$importedCount} tasks between {$startDate} and {$endDate}.",
                'imported_count' => $importedCount,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to import events',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function runImport($events)
    {
        foreach ($events as $event) {
            $start = $event->start->dateTime ?? $event->start->date;
            // Normalize to day to avoid duplicates for the same calendar entry within a day
            $completedAt = Carbon::parse($start)->startOfDay();
            $projectCode = substr(preg_replace('/[^A-Z]/', '', $event->getSummary()), 0, 3);

            $project = Project::where('project_code', $projectCode)->first();

            if ($project) {
                $name = preg_replace('/[^a-zA-Z0-9\s.]/', '', substr($event->getSummary(), 4));
                // Filter out all text starting with a /
                $name = preg_replace('/\/\S*/', '', $name);

                Task::updateOrCreate(
                    [
                        'icalUID' => $event->iCalUID,
                        'completed_at' => $completedAt,
                    ],
                    [
                        'name' => $name,
                        'description' => $event->getDescription() ?? '',
                        'project_id' => $project->id,
                        'minutes' => isset($event->start->dateTime) && isset($event->end->dateTime) ? ceil((strtotime($event->end->dateTime) - strtotime($event->start->dateTime)) / 60) : 0,
                        'is_service' => str_contains($event->getSummary(), '🆓') ? 1 : 0,
                    ]
                );
            }
        }
    }

    private function runImportWithCount($events): int
    {
        $importedCount = 0;

        foreach ($events as $event) {
            $start = $event->start->dateTime ?? $event->start->date;
            $completedAt = Carbon::parse($start)->startOfMinute();
            $projectCode = substr(preg_replace('/[^A-Z]/', '', $event->getSummary()), 0, 3);

            $project = Project::where('project_code', $projectCode)->first();

            if ($project) {
                $name = preg_replace('/[^a-zA-Z0-9\s.]/', '', substr($event->getSummary(), 4));
                // Filter out all text starting with a /
                $name = preg_replace('/\/\S*/', '', $name);

                $created = Task::updateOrCreate(
                    [
                        'icalUID' => $event->iCalUID,
                        'completed_at' => $completedAt,
                    ],
                    [
                        'name' => $name,
                        'description' => $event->getDescription() ?? '',
                        'project_id' => $project->id,
                        'minutes' => isset($event->start->dateTime) && isset($event->end->dateTime) ? ceil((strtotime($event->end->dateTime) - strtotime($event->start->dateTime)) / 60) : 0,
                        'is_service' => str_contains($event->getSummary(), '🆓') ? 1 : 0,
                    ]
                );

                if ($created->wasRecentlyCreated === true) {
                    $importedCount++;
                }
            }
        }

        return $importedCount;
    }
}
