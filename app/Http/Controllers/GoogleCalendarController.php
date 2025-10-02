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

    public function runImport($events)
    {
        foreach ($events as $event) {
            $start = $event->start->dateTime ?? $event->start->date;
            $projectCode = substr(preg_replace('/[^A-Z]/', '', $event->getSummary()), 0, 3);

            $project = Project::where('project_code', $projectCode)->first();

            if ($project) {
                if (!Task::where('icalUID', $event->iCalUID)->exists()) {
                    $name = preg_replace('/[^a-zA-Z0-9\s.]/', '', substr($event->getSummary(), 4));
                    // Filter out all text starting with a /
                    $name = preg_replace('/\/\S*/', '', $name);

                    Task::create([
                        'name' => $name,
                        'description' => $event->getDescription() ?? '',
                        'completed_at' => $start,
                        'project_id' => $project->id,
                        'icalUID' => $event->iCalUID,
                        'minutes' => isset($event->start->dateTime) && isset($event->end->dateTime) ? ceil((strtotime($event->end->dateTime) - strtotime($event->start->dateTime)) / 60) : 0,
                        'is_service' => str_contains($event->getSummary(), '🆓') ? 1 : 0,
                    ]);
                }
            }
        }
    }

    private function runImportWithCount($events): int
    {
        $importedCount = 0;

        foreach ($events as $event) {
            $start = $event->start->dateTime ?? $event->start->date;
            $projectCode = substr(preg_replace('/[^A-Z]/', '', $event->getSummary()), 0, 3);

            $project = Project::where('project_code', $projectCode)->first();

            if ($project) {
                if (!Task::where('icalUID', $event->iCalUID)->exists()) {
                    $name = preg_replace('/[^a-zA-Z0-9\s.]/', '', substr($event->getSummary(), 4));
                    // Filter out all text starting with a /
                    $name = preg_replace('/\/\S*/', '', $name);

                    Task::create([
                        'name' => $name,
                        'description' => $event->getDescription() ?? '',
                        'completed_at' => $start,
                        'project_id' => $project->id,
                        'icalUID' => $event->iCalUID,
                        'minutes' => isset($event->start->dateTime) && isset($event->end->dateTime) ? ceil((strtotime($event->end->dateTime) - strtotime($event->start->dateTime)) / 60) : 0,
                        'is_service' => str_contains($event->getSummary(), '🆓') ? 1 : 0,
                    ]);

                    $importedCount++;
                }
            }
        }

        return $importedCount;
    }
}
