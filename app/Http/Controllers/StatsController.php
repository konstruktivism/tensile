<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function getHoursPerWeek()
    {
        $tasks = DB::table('tasks')
            ->select(
                DB::raw('WEEKOFYEAR(completed_at) as week'),
                DB::raw('SUM(minutes) as total_minutes'),
                DB::raw('SUM(is_service) as service_tasks'),
                DB::raw('COUNT(*) as total_tasks'),
                DB::raw('(SUM(is_service) / COUNT(*)) * 100 as service_percentage')
            )
            ->groupBy('week')
            ->get();

        return response()->json($tasks);
    }
}
