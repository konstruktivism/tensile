<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function getHoursPerWeek()
    {
        $tasks = DB::table('tasks')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->select(
                DB::raw('WEEKOFYEAR(tasks.completed_at) as week'),
                DB::raw('SUM(tasks.minutes) as total_minutes'),
                DB::raw('SUM(tasks.is_service) as service_tasks'),
                DB::raw('COUNT(*) as total_tasks'),
                DB::raw('(SUM(tasks.is_service) / COUNT(*)) * 100 as service_percentage'),
                DB::raw('SUM(CASE WHEN projects.is_internal = true THEN 1 ELSE 0 END) as internal_tasks')
            )
            ->groupBy('week')
            ->get();

        return response()->json($tasks);
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
                ->get(['completed_at', 'minutes']);

            $weeklyTasks = $tasks->groupBy(function ($task) {
                return \Carbon\Carbon::parse($task->completed_at)->weekOfYear;
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
