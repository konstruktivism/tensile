<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;

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

    public function importEventsLastMonth():  \Illuminate\Http\JsonResponse
    {
        $events = $this->googleCalendarService->getEvents(100, Carbon::now()->subMonth()->daysInMonth);

        dd($events);

        $this->runImport($events);

        return response()->json(['message' => 'Events imported of the last month.']);
    }

    public function runImport($events)
    {
        foreach ($events as $event) {
            $start = $event->start->dateTime ?? $event->start->date;
            $projectCode = substr(preg_replace('/[^A-Z]/', '', $event->getSummary()), 0, 3);

            $project = Project::where('project_code', $projectCode)->first();

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

        return;
    }
}
