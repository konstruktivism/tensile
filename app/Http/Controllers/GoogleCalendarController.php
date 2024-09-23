<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;

class GoogleCalendarController extends Controller
{
    protected $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    public function importEvents(): void
    {
        $events = $this->googleCalendarService->getEvents();

        $this->runImport($events);
    }

    public function importEvents30Days(): void
    {
        $events = $this->googleCalendarService->getEvents();

        $this->runImport($events);
    }

    public function runImport($events): \Illuminate\Http\JsonResponse
    {
        if (empty($events)) {
            return response()->json(['message' => 'No upcoming events found.']);
        }

        foreach ($events as $event) {
            $start = $event->start->dateTime ?? $event->start->date;
            // Create a new task with the event data

            ray($event);

            $projectCode = substr(preg_replace('/[^A-Z]/', '', $event->getSummary()), 0, 3);
            $project = Project::where('project_code', $projectCode)->first();

            ray($project);

            if($project) {
                if (!Task::where('icalUID', $event->iCalUID)->exists()) {
                    Task::create([
                        'name' => substr($event->getSummary(), 4),
                        'description' => $event->getDescription() ?? '',
                        'completed_at' => $start,
                        'project_id' => $project->id,
                        'icalUID' => $event->iCalUID,
                        'minutes' => isset($event->start->dateTime) && isset($event->end->dateTime) ? ceil((strtotime($event->end->dateTime) - strtotime($event->start->dateTime)) / 60) : 0,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Events imported successfully.']);
    }
}
