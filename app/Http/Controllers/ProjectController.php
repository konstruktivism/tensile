<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use Carbon\Carbon;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects linked to the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $projects = Auth::user()->organisation?->projects;

        return view('projects', compact('projects'));
    }

    /**
     * Display the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function read(Project $project)
    {
        // Group tasks by the week number of the completed_at date
        $tasksByWeek = $project->tasks->groupBy(function ($task) {
            return Carbon::parse($task->completed_at)->format('W');
        });

        $tasksByWeekWithHours = $tasksByWeek->map(function ($tasks, $week) {
            $totalHours = $tasks->sum('hours');
            return [
                'tasks' => $tasks,
                'total_hours' => $totalHours
            ];
        });

        // Return the view with the project
        return view('project', compact('project', 'tasksByWeekWithHours'));
    }

}
