<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use Carbon\Carbon;
use App\Models\Task;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects linked to the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $projects = Auth::user()->projects;

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
        if (!Auth::user()->projects->contains($project)) {
            abort(403, 'Unauthorized action.');
        }

        // Group tasks by the month and week number of the completed_at date
        $tasksByMonth = $project->tasks->groupBy(function ($task) {
            $date = Carbon::parse($task->completed_at);
            return $date->format('Y-m');
        });

        $tasksByMonthAndWeekWithMinutes = $tasksByMonth->map(function ($tasks, $month) {
            $tasksByWeek = $tasks->groupBy(function ($task) {
                $date = Carbon::parse($task->completed_at);
                return $date->format('W');
            });

            return $tasksByWeek->map(function ($tasks, $week) {
                $totalMinutes = $tasks->sum('minutes');
                return [
                    'tasks' => $tasks,
                    'total_minutes' => $totalMinutes
                ];
            })->sortKeys();
        })->sortKeys();

        // Return the view with the project
        return view('project', compact('project', 'tasksByMonthAndWeekWithMinutes'));
    }

    /**
     * Display the specified week of the project.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $week
     * @return \Illuminate\View\View
     */
    public function viewWeek(Project $project, $week)
    {
        if (!Auth::user()->projects->contains($project)) {
            abort(403, 'Unauthorized action.');
        }

        $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, $week)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $tasks = $project->tasks()->whereBetween('completed_at', [$startOfWeek, $endOfWeek])->get();

        $previousWeekTasks = $this->getPreviousWeekTasks($project->id, $week);

        $nextWeekTasks = $this->getNextWeekTasks($project->id, $week);

        return view('projects.week', compact('project', 'tasks', 'week', 'previousWeekTasks', 'nextWeekTasks'));
    }

    public function getPreviousWeekTasks($projectId, $week)
    {
        $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, $week)->startOfWeek()->subWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();
        return Task::where('project_id', $projectId)
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->get();
    }
    public function getNextWeekTasks($projectId, $week)
    {
        $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, $week)->startOfWeek()->addWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();
        return Task::where('project_id', $projectId)
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->get();
    }
}
