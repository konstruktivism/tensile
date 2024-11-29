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
        $projects = Auth::user()->projects->map(function ($project) {
            $project->is_active = $project->tasks()->where('completed_at', '>=', Carbon::now()->subMonth())->exists();
            return $project;
        })->sortByDesc('is_active');

        $activeProjects = $projects->where('is_active', true);
        $inactiveProjects = $projects->where('is_active', false);

        return view('projects', compact('activeProjects', 'inactiveProjects'));
    }

    /**
     * Display the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function read(Project $project)
    {
        $this->authorizeProject($project);

        $tasksByMonthAndWeekWithMinutes = $this->groupTasksByMonthAndWeek($project->tasks);

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
        $this->authorizeProject($project);

        $tasks = $this->getTasksForWeek($project, $week);
        $previousWeekTasks = $this->getTasksForWeek($project, $week - 1);
        $nextWeekTasks = $this->getTasksForWeek($project, $week + 1);

        return view('projects.week', compact('project', 'tasks', 'week', 'previousWeekTasks', 'nextWeekTasks'));
    }

    protected function authorizeProject(Project $project): void
    {
        if (!Auth::user()->projects->contains($project)) {
            abort(403, 'Unauthorized action.');
        }
    }

    protected function groupTasksByMonthAndWeek($tasks)
    {
        return $tasks->groupBy(function ($task) {
            return Carbon::parse($task->completed_at)->format('Y-m');
        })->map(function ($tasks, $month) {
            return $tasks->groupBy(function ($task) {
                return Carbon::parse($task->completed_at)->format('W');
            })->map(function ($tasks) {
                return [
                    'tasks' => $tasks,
                    'total_minutes' => $tasks->sum('minutes'),
                    'total_minutes_without_service' => $tasks->where('is_service', '!=', 1)->sum('minutes')
                ];
            })->sortKeys();
        })->sortKeysDesc();
    }

    protected function getTasksForWeek(Project $project, $week)
    {
        $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, $week)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        return $project->tasks()->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->orderBy('completed_at')
            ->get();
    }
}
