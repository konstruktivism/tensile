<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function getHoursPerWeek(?int $year = null): \Illuminate\Http\JsonResponse
    {
        $year = $year ?? now()->year;

        $tasks = DB::table('tasks')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $year)
            ->get([
                'tasks.completed_at',
                'tasks.minutes',
                'tasks.is_service',
                'projects.is_internal',
            ]);

        $weeklyStats = $tasks
            ->groupBy(function ($task) {
                $date = Carbon::parse($task->completed_at);

                return $date->format('Y-W');
            })
            ->map(function ($weekTasks, $yearWeek) {
                $date = Carbon::parse($weekTasks->first()->completed_at);
                $week = (int) $date->weekOfYear;

                $totalMinutes = (int) $weekTasks->sum('minutes');
                $totalTasks = $weekTasks->count();
                $serviceTasks = (int) $weekTasks->sum('is_service');
                $internalTasks = $weekTasks->where('is_internal', true)->count();

                $servicePercentage = $totalTasks === 0
                    ? 0
                    : round(($serviceTasks / $totalTasks) * 100, 2);

                return [
                    'week' => $week,
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

    public function getRevenuePerWeek(?int $year = null): \Illuminate\Http\JsonResponse
    {
        $year = $year ?? now()->year;

        $projects = Project::where('is_internal', false)
            ->get();

        // Loop through projects and calculate revenue per week of all tasks in that week
        $revenue = [];

        foreach ($projects as $project) {
            $tasks = DB::table('tasks')
                ->where('project_id', $project->id)
                ->where('is_service', false)
                ->whereYear('completed_at', $year)
                ->get(['completed_at', 'minutes']);

            $weeklyTasks = $tasks->groupBy(function ($task) {
                $date = Carbon::parse($task->completed_at);

                return $date->format('Y-W');
            });

            foreach ($weeklyTasks as $yearWeek => $tasks) {
                $date = Carbon::parse($tasks->first()->completed_at);
                $week = (int) $date->weekOfYear;

                $totalMinutes = $tasks->sum('minutes');
                $revenue[$week] = $revenue[$week] ?? 0;
                $revenue[$week] += round($totalMinutes / 60 * $project->hour_tariff);
            }
        }

        ksort($revenue);

        return response()->json($revenue);
    }
}
