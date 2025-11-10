<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function getHoursPerWeek()
    {
        $tasks = DB::table('tasks')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', now()->year)
            ->get([
                'tasks.completed_at',
                'tasks.minutes',
                'tasks.is_service',
                'projects.is_internal',
            ]);

        $weeklyStats = $tasks
            ->groupBy(function ($task) {
                return Carbon::parse($task->completed_at)->weekOfYear;
            })
            ->map(function ($weekTasks, $week) {
                $totalMinutes = (int) $weekTasks->sum('minutes');
                $totalTasks = $weekTasks->count();
                $serviceTasks = (int) $weekTasks->sum('is_service');
                $internalTasks = $weekTasks->where('is_internal', true)->count();

                $servicePercentage = $totalTasks === 0
                    ? 0
                    : round(($serviceTasks / $totalTasks) * 100, 2);

                return [
                    'week' => (int) $week,
                    'total_minutes' => $totalMinutes,
                    'service_tasks' => $serviceTasks,
                    'total_tasks' => $totalTasks,
                    'service_percentage' => $servicePercentage,
                    'internal_tasks' => $internalTasks,
                ];
            })
            ->sortKeys()
            ->values();

        return response()->json($weeklyStats);
    }

    public function getRevenuePerWeek()
    {
        $projects = Project::where('is_internal', false)
            ->get();

        // Loop through projects and calculate revenue per week of all tasks in that week
        $revenue = [];

        foreach ($projects as $project) {
            $tasks = DB::table('tasks')
                ->where('project_id', $project->id)
                ->where('is_service', false)
                ->whereYear('completed_at', now()->year)
                ->get(['completed_at', 'minutes']);

            $weeklyTasks = $tasks->groupBy(function ($task) {
                return Carbon::parse($task->completed_at)->weekOfYear;
            });

            foreach ($weeklyTasks as $week => $tasks) {
                $totalMinutes = $tasks->sum('minutes');
                $revenue[$week] = $revenue[$week] ?? 0;
                $revenue[$week] += round($totalMinutes / 60 * $project->hour_tariff);
            }
        }

        ksort($revenue);

        return response()->json($revenue);
    }
}
