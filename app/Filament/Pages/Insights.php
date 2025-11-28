<?php

namespace App\Filament\Pages;

use App\Helpers\CurrencyHelper;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Insights extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.insights';

    protected static ?string $navigationLabel = 'Insights';

    protected static ?int $navigationSort = 4;

    public ?int $selectedYear = null;

    public function mount(): void
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
    }

    public function updatedSelectedYear(): void
    {
        // Reset any cached data when year changes
    }

    public function getYearOptions(): array
    {
        $availableYears = Task::query()
            ->whereNotNull('completed_at')
            ->selectRaw('YEAR(completed_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return array_combine($availableYears, $availableYears);
    }

    public function getYearStats(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $stats = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->select([
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('COUNT(tasks.id) as total_tasks'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 THEN tasks.minutes ELSE 0 END) / 60 as billable_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 1 THEN tasks.minutes ELSE 0 END) / 60 as service_hours'),
                DB::raw('SUM(CASE WHEN projects.is_internal = 1 THEN tasks.minutes ELSE 0 END) / 60 as internal_hours'),
            ])
            ->first();

        return [
            'total_hours' => round($stats->total_hours ?? 0, 2),
            'total_tasks' => $stats->total_tasks ?? 0,
            'revenue' => round($stats->revenue ?? 0, 2),
            'billable_hours' => round($stats->billable_hours ?? 0, 2),
            'service_hours' => round($stats->service_hours ?? 0, 2),
            'internal_hours' => round($stats->internal_hours ?? 0, 2),
        ];
    }

    public function getTopProjects(int $limit = 5): array
    {
        $year = $this->selectedYear ?? now()->year;

        $projects = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->join('organisations', 'projects.organisation_id', '=', 'organisations.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->select([
                'projects.id',
                'projects.name',
                'organisations.name as organisation_name',
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
            ])
            ->groupBy('projects.id', 'projects.name', 'organisations.name')
            ->orderByDesc('total_hours')
            ->limit($limit)
            ->get();

        $totalHours = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->sum(DB::raw('minutes / 60'));

        return $projects->map(function ($project) use ($totalHours) {
            $percentage = $totalHours > 0 ? round(($project->total_hours / $totalHours) * 100, 1) : 0;

            return [
                'id' => $project->id,
                'name' => $project->name,
                'organisation' => $project->organisation_name,
                'hours' => round($project->total_hours, 2),
                'revenue' => round($project->revenue ?? 0, 2),
                'percentage' => $percentage,
            ];
        })->toArray();
    }

    public function getTopOrganisations(int $limit = 5): array
    {
        $year = $this->selectedYear ?? now()->year;

        $organisations = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->join('organisations', 'projects.organisation_id', '=', 'organisations.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->select([
                'organisations.id',
                'organisations.name',
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
            ])
            ->groupBy('organisations.id', 'organisations.name')
            ->orderByDesc('total_hours')
            ->limit($limit)
            ->get();

        $totalHours = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->sum(DB::raw('minutes / 60'));

        return $organisations->map(function ($org) use ($totalHours) {
            $percentage = $totalHours > 0 ? round(($org->total_hours / $totalHours) * 100, 1) : 0;

            return [
                'id' => $org->id,
                'name' => $org->name,
                'hours' => round($org->total_hours, 2),
                'revenue' => round($org->revenue ?? 0, 2),
                'percentage' => $percentage,
            ];
        })->toArray();
    }

    public function getBusiestMonth(): ?array
    {
        $year = $this->selectedYear ?? now()->year;

        $monthlyStats = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->selectRaw('MONTH(completed_at) as month')
            ->selectRaw('SUM(minutes) / 60 as total_hours')
            ->groupBy('month')
            ->orderByDesc('total_hours')
            ->first();

        if (! $monthlyStats) {
            return null;
        }

        $monthName = Carbon::create($year, $monthlyStats->month, 1)->format('F');

        return [
            'month' => $monthlyStats->month,
            'month_name' => $monthName,
            'hours' => round($monthlyStats->total_hours, 2),
        ];
    }

    public function getBusiestWeek(): ?array
    {
        $year = $this->selectedYear ?? now()->year;

        $weeklyStats = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->selectRaw('WEEK(completed_at, 3) as week')
            ->selectRaw('SUM(minutes) / 60 as total_hours')
            ->groupBy('week')
            ->orderByDesc('total_hours')
            ->first();

        if (! $weeklyStats) {
            return null;
        }

        return [
            'week' => $weeklyStats->week,
            'hours' => round($weeklyStats->total_hours, 2),
        ];
    }

    public function getServiceBreakdown(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $stats = $this->getYearStats();

        $totalHours = $stats['total_hours'];
        if ($totalHours == 0) {
            return [
                'service' => 0,
                'billable' => 0,
                'internal' => 0,
            ];
        }

        return [
            'service' => round(($stats['service_hours'] / $totalHours) * 100, 1),
            'billable' => round(($stats['billable_hours'] / $totalHours) * 100, 1),
            'internal' => round(($stats['internal_hours'] / $totalHours) * 100, 1),
        ];
    }

    public function getMonthlyTrends(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $monthlyData = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->selectRaw('MONTH(tasks.completed_at) as month')
            ->selectRaw('SUM(tasks.minutes) / 60 as total_hours')
            ->selectRaw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $trends = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $monthlyData->firstWhere('month', $month);
            $trends[] = [
                'month' => Carbon::create($year, $month, 1)->format('M'),
                'hours' => round($monthData->total_hours ?? 0, 2),
                'revenue' => round($monthData->revenue ?? 0, 2),
            ];
        }

        return $trends;
    }

    public function getMarketingInsights(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $stats = $this->getYearStats();
        $topProject = $this->getTopProjects(1);
        $busiestMonth = $this->getBusiestMonth();
        $busiestWeek = $this->getBusiestWeek();
        $breakdown = $this->getServiceBreakdown();
        $weekendStats = $this->getWeekendStats();
        $eveningStats = $this->getEveningWorkStats();
        $dayOfWeekStats = $this->getDayOfWeekStats();
        $workingDaysStats = $this->getWorkingDaysStats();
        $hourDistribution = $this->getHourDistribution();

        $insights = [];

        if ($stats['total_hours'] > 0) {
            $insights[] = "You worked {$stats['total_hours']} hours in {$year}";
        }

        if ($stats['total_tasks'] > 0) {
            $insights[] = "You completed {$stats['total_tasks']} tasks this year";
        }

        if ($stats['revenue'] > 0) {
            $insights[] = 'Total revenue: '.CurrencyHelper::formatCurrency($stats['revenue']);
        }

        if (! empty($topProject)) {
            $project = $topProject[0];
            $insights[] = "Top project: {$project['name']} ({$project['percentage']}% of total hours)";
        }

        if ($busiestMonth) {
            $insights[] = "Your busiest month was {$busiestMonth['month_name']} with {$busiestMonth['hours']} hours";
        }

        if ($busiestWeek) {
            $insights[] = "Your busiest week was Week {$busiestWeek['week']} with {$busiestWeek['hours']} hours";
        }

        if ($workingDaysStats['days_worked'] > 0) {
            $insights[] = "You worked {$workingDaysStats['days_worked']} days ({$workingDaysStats['work_percent']}% of the year)";
        }

        if ($workingDaysStats['avg_hours_per_day'] > 0) {
            $insights[] = "Average {$workingDaysStats['avg_hours_per_day']} hours per working day";
        }

        if (! empty($dayOfWeekStats)) {
            $mostProductiveDay = $dayOfWeekStats[0];
            $insights[] = "Most productive day: {$mostProductiveDay['day']} ({$mostProductiveDay['hours']} hours)";
        }

        if ($weekendStats['weekend_hours'] > 0) {
            $insights[] = "You worked {$weekendStats['weekend_days']} weekends ({$weekendStats['weekend_percent']}% of total hours)";
        }

        if ($eveningStats['evening_hours'] > 0) {
            $insights[] = "Evening work: {$eveningStats['evening_hours']} hours across {$eveningStats['evening_days']} days";
        }

        if ($hourDistribution['afternoon_percent'] > 40) {
            $insights[] = "You're an afternoon person! {$hourDistribution['afternoon_percent']}% of your work happens in the afternoon";
        } elseif ($hourDistribution['morning_percent'] > 40) {
            $insights[] = "Early bird! {$hourDistribution['morning_percent']}% of your work happens in the morning";
        } elseif ($hourDistribution['evening_percent'] > 30) {
            $insights[] = "Night owl! {$hourDistribution['evening_percent']}% of your work happens in the evening";
        }

        if ($breakdown['service'] > 0 || $breakdown['billable'] > 0 || $breakdown['internal'] > 0) {
            $parts = [];
            if ($breakdown['service'] > 0) {
                $parts[] = "Service: {$breakdown['service']}%";
            }
            if ($breakdown['billable'] > 0) {
                $parts[] = "Billable: {$breakdown['billable']}%";
            }
            if ($breakdown['internal'] > 0) {
                $parts[] = "Internal: {$breakdown['internal']}%";
            }
            if (! empty($parts)) {
                $insights[] = implode(' | ', $parts);
            }
        }

        return $insights;
    }

    public function getProjectDistributionData(): array
    {
        $topProjects = $this->getTopProjects(10);
        $labels = array_map(fn ($p) => $p['name'], $topProjects);
        $data = array_map(fn ($p) => $p['hours'], $topProjects);

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function getHourDistribution(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $tasks = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->select(['completed_at', 'minutes'])
            ->get();

        $distribution = [
            'morning' => 0,      // 6-12
            'afternoon' => 0,    // 12-18
            'evening' => 0,      // 18-22
            'night' => 0,        // 22-6
        ];

        foreach ($tasks as $task) {
            $hour = (int) Carbon::parse($task->completed_at)->format('H');
            $hours = $task->minutes / 60;

            if ($hour >= 6 && $hour < 12) {
                $distribution['morning'] += $hours;
            } elseif ($hour >= 12 && $hour < 18) {
                $distribution['afternoon'] += $hours;
            } elseif ($hour >= 18 && $hour < 22) {
                $distribution['evening'] += $hours;
            } else {
                $distribution['night'] += $hours;
            }
        }

        $total = array_sum($distribution);

        return [
            'morning' => round($distribution['morning'], 2),
            'afternoon' => round($distribution['afternoon'], 2),
            'evening' => round($distribution['evening'], 2),
            'night' => round($distribution['night'], 2),
            'morning_percent' => $total > 0 ? round(($distribution['morning'] / $total) * 100, 1) : 0,
            'afternoon_percent' => $total > 0 ? round(($distribution['afternoon'] / $total) * 100, 1) : 0,
            'evening_percent' => $total > 0 ? round(($distribution['evening'] / $total) * 100, 1) : 0,
            'night_percent' => $total > 0 ? round(($distribution['night'] / $total) * 100, 1) : 0,
        ];
    }

    public function getWeekendStats(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $weekendTasks = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->whereRaw('WEEKDAY(completed_at) IN (5, 6)')
            ->select([
                DB::raw('SUM(minutes) / 60 as weekend_hours'),
                DB::raw('COUNT(DISTINCT DATE(completed_at)) as weekend_days'),
            ])
            ->first();

        $weekdayTasks = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->whereRaw('WEEKDAY(completed_at) NOT IN (5, 6)')
            ->select([
                DB::raw('SUM(minutes) / 60 as weekday_hours'),
                DB::raw('COUNT(DISTINCT DATE(completed_at)) as weekday_days'),
            ])
            ->first();

        $totalWeekendDays = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->whereRaw('WEEKDAY(completed_at) IN (5, 6)')
            ->selectRaw('COUNT(DISTINCT DATE(completed_at)) as count')
            ->value('count') ?? 0;

        $totalWeekdayDays = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->whereRaw('WEEKDAY(completed_at) NOT IN (5, 6)')
            ->selectRaw('COUNT(DISTINCT DATE(completed_at)) as count')
            ->value('count') ?? 0;

        $totalHours = ($weekendTasks->weekend_hours ?? 0) + ($weekdayTasks->weekday_hours ?? 0);

        return [
            'weekend_hours' => round($weekendTasks->weekend_hours ?? 0, 2),
            'weekday_hours' => round($weekdayTasks->weekday_hours ?? 0, 2),
            'weekend_days' => $weekendTasks->weekend_days ?? 0,
            'weekday_days' => $weekdayTasks->weekday_days ?? 0,
            'weekend_percent' => $totalHours > 0 ? round((($weekendTasks->weekend_hours ?? 0) / $totalHours) * 100, 1) : 0,
            'weekday_percent' => $totalHours > 0 ? round((($weekdayTasks->weekday_hours ?? 0) / $totalHours) * 100, 1) : 0,
            'total_weekend_days' => $totalWeekendDays,
            'total_weekday_days' => $totalWeekdayDays,
        ];
    }

    public function getEveningWorkStats(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $eveningTasks = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->whereRaw('HOUR(completed_at) >= 18')
            ->select([
                DB::raw('SUM(minutes) / 60 as evening_hours'),
                DB::raw('COUNT(DISTINCT DATE(completed_at)) as evening_days'),
            ])
            ->first();

        $lateNightTasks = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->whereRaw('HOUR(completed_at) >= 22 OR HOUR(completed_at) < 6')
            ->select([
                DB::raw('SUM(minutes) / 60 as late_night_hours'),
                DB::raw('COUNT(DISTINCT DATE(completed_at)) as late_night_days'),
            ])
            ->first();

        $stats = $this->getYearStats();
        $totalHours = $stats['total_hours'];

        return [
            'evening_hours' => round($eveningTasks->evening_hours ?? 0, 2),
            'evening_days' => $eveningTasks->evening_days ?? 0,
            'evening_percent' => $totalHours > 0 ? round((($eveningTasks->evening_hours ?? 0) / $totalHours) * 100, 1) : 0,
            'late_night_hours' => round($lateNightTasks->late_night_hours ?? 0, 2),
            'late_night_days' => $lateNightTasks->late_night_days ?? 0,
            'late_night_percent' => $totalHours > 0 ? round((($lateNightTasks->late_night_hours ?? 0) / $totalHours) * 100, 1) : 0,
        ];
    }

    public function getDayOfWeekStats(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $dayStats = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->selectRaw('WEEKDAY(completed_at) as day_of_week')
            ->selectRaw('SUM(minutes) / 60 as total_hours')
            ->selectRaw('COUNT(DISTINCT DATE(completed_at)) as days_worked')
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $result = [];

        foreach ($dayStats as $stat) {
            $dayName = $dayNames[$stat->day_of_week] ?? 'Unknown';
            $result[] = [
                'day' => $dayName,
                'day_index' => $stat->day_of_week,
                'hours' => round($stat->total_hours, 2),
                'days_worked' => $stat->days_worked,
            ];
        }

        usort($result, fn ($a, $b) => $b['hours'] <=> $a['hours']);

        return $result;
    }

    public function getPeakHours(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $hourlyStats = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->selectRaw('HOUR(completed_at) as hour')
            ->selectRaw('SUM(minutes) / 60 as total_hours')
            ->groupBy('hour')
            ->orderByDesc('total_hours')
            ->limit(3)
            ->get();

        return $hourlyStats->map(function ($stat) {
            return [
                'hour' => $stat->hour,
                'hour_label' => $stat->hour.':00',
                'hours' => round($stat->total_hours, 2),
            ];
        })->toArray();
    }

    public function getWorkingDaysStats(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $uniqueDays = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->selectRaw('COUNT(DISTINCT DATE(completed_at)) as total_days')
            ->value('total_days') ?? 0;

        $yearStart = Carbon::create($year, 1, 1);
        $yearEnd = Carbon::create($year, 12, 31);
        $totalDaysInYear = $yearStart->diffInDays($yearEnd) + 1;

        $stats = $this->getYearStats();
        $avgHoursPerDay = $uniqueDays > 0 ? round($stats['total_hours'] / $uniqueDays, 2) : 0;

        return [
            'days_worked' => $uniqueDays,
            'days_off' => $totalDaysInYear - $uniqueDays,
            'total_days' => $totalDaysInYear,
            'work_percent' => $totalDaysInYear > 0 ? round(($uniqueDays / $totalDaysInYear) * 100, 1) : 0,
            'avg_hours_per_day' => $avgHoursPerDay,
        ];
    }

    public function getRevenueEfficiency(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $stats = $this->getYearStats();

        $billableHours = $stats['billable_hours'];
        $revenue = $stats['revenue'];
        $totalHours = $stats['total_hours'];

        $revenuePerHour = $billableHours > 0 ? round($revenue / $billableHours, 2) : 0;
        $revenuePerTotalHour = $totalHours > 0 ? round($revenue / $totalHours, 2) : 0;
        $utilizationRate = $totalHours > 0 ? round(($billableHours / $totalHours) * 100, 1) : 0;

        return [
            'revenue_per_billable_hour' => $revenuePerHour,
            'revenue_per_total_hour' => $revenuePerTotalHour,
            'utilization_rate' => $utilizationRate,
            'billable_hours' => $billableHours,
            'total_hours' => $totalHours,
        ];
    }

    public function getProjectProfitability(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $projects = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->where('projects.is_fixed', 0)
            ->where('projects.is_internal', 0)
            ->select([
                'projects.id',
                'projects.name',
                'projects.hour_tariff',
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 THEN tasks.minutes ELSE 0 END) / 60 as billable_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
            ])
            ->groupBy('projects.id', 'projects.name', 'projects.hour_tariff')
            ->havingRaw('SUM(tasks.minutes) > 0')
            ->get();

        $projectsWithEfficiency = $projects->map(function ($project) {
            $billableHours = $project->billable_hours ?? 0;
            $revenuePerHour = $billableHours > 0 ? round($project->revenue / $billableHours, 2) : 0;
            $efficiency = $project->hour_tariff > 0 ? round(($revenuePerHour / $project->hour_tariff) * 100, 1) : 0;

            return [
                'id' => $project->id,
                'name' => $project->name,
                'hour_tariff' => $project->hour_tariff,
                'total_hours' => round($project->total_hours, 2),
                'billable_hours' => round($billableHours, 2),
                'revenue' => round($project->revenue ?? 0, 2),
                'revenue_per_hour' => $revenuePerHour,
                'efficiency_percent' => $efficiency,
            ];
        })->sortByDesc('revenue_per_hour')->values()->toArray();

        $avgHourlyRate = count($projectsWithEfficiency) > 0
            ? round(array_sum(array_column($projectsWithEfficiency, 'revenue_per_hour')) / count($projectsWithEfficiency), 2)
            : 0;

        return [
            'projects' => array_slice($projectsWithEfficiency, 0, 10),
            'avg_hourly_rate' => $avgHourlyRate,
            'total_projects' => count($projectsWithEfficiency),
        ];
    }

    public function getClientValueAnalysis(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $clients = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->join('organisations', 'projects.organisation_id', '=', 'organisations.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->where('projects.is_internal', 0)
            ->select([
                'organisations.id',
                'organisations.name',
                DB::raw('COUNT(DISTINCT projects.id) as project_count'),
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
                DB::raw('COUNT(DISTINCT DATE(tasks.completed_at)) as days_active'),
            ])
            ->groupBy('organisations.id', 'organisations.name')
            ->havingRaw('SUM(tasks.minutes) > 0')
            ->get();

        $clientsWithMetrics = $clients->map(function ($client) {
            $revenue = $client->revenue ?? 0;
            $hours = $client->total_hours ?? 0;
            $revenuePerHour = $hours > 0 ? round($revenue / $hours, 2) : 0;

            return [
                'id' => $client->id,
                'name' => $client->name,
                'project_count' => $client->project_count,
                'total_hours' => round($hours, 2),
                'revenue' => round($revenue, 2),
                'revenue_per_hour' => $revenuePerHour,
                'days_active' => $client->days_active,
                'avg_hours_per_day' => $client->days_active > 0 ? round($hours / $client->days_active, 2) : 0,
            ];
        })->sortByDesc('revenue')->values()->toArray();

        $totalRevenue = array_sum(array_column($clientsWithMetrics, 'revenue'));
        $topClientsRevenue = array_sum(array_column(array_slice($clientsWithMetrics, 0, 3), 'revenue'));
        $revenueConcentration = $totalRevenue > 0 ? round(($topClientsRevenue / $totalRevenue) * 100, 1) : 0;

        return [
            'clients' => array_slice($clientsWithMetrics, 0, 10),
            'total_clients' => count($clientsWithMetrics),
            'revenue_concentration' => $revenueConcentration,
            'top_3_revenue_percent' => $revenueConcentration,
        ];
    }

    public function getGrowthTrends(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $prevYear = $year - 1;

        $currentYearStats = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->select([
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('COUNT(tasks.id) as total_tasks'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
            ])
            ->first();

        $prevYearStats = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $prevYear)
            ->whereNotNull('tasks.completed_at')
            ->select([
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('COUNT(tasks.id) as total_tasks'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
            ])
            ->first();

        $currentHours = $currentYearStats->total_hours ?? 0;
        $prevHours = $prevYearStats->total_hours ?? 0;
        $hoursGrowth = $prevHours > 0 ? round((($currentHours - $prevHours) / $prevHours) * 100, 1) : 0;

        $currentRevenue = $currentYearStats->revenue ?? 0;
        $prevRevenue = $prevYearStats->revenue ?? 0;
        $revenueGrowth = $prevRevenue > 0 ? round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;

        $currentTasks = $currentYearStats->total_tasks ?? 0;
        $prevTasks = $prevYearStats->total_tasks ?? 0;
        $tasksGrowth = $prevTasks > 0 ? round((($currentTasks - $prevTasks) / $prevTasks) * 100, 1) : 0;

        return [
            'hours_growth' => $hoursGrowth,
            'revenue_growth' => $revenueGrowth,
            'tasks_growth' => $tasksGrowth,
            'current_hours' => round($currentHours, 2),
            'prev_hours' => round($prevHours, 2),
            'current_revenue' => round($currentRevenue, 2),
            'prev_revenue' => round($prevRevenue, 2),
            'has_prev_year_data' => $prevHours > 0,
        ];
    }

    public function getQuarterlyTrends(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $quarterlyData = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->selectRaw('QUARTER(tasks.completed_at) as quarter')
            ->selectRaw('SUM(tasks.minutes) / 60 as total_hours')
            ->selectRaw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue')
            ->selectRaw('COUNT(tasks.id) as total_tasks')
            ->groupBy('quarter')
            ->orderBy('quarter')
            ->get();

        $quarters = [];
        for ($q = 1; $q <= 4; $q++) {
            $quarterData = $quarterlyData->firstWhere('quarter', $q);
            $quarters[] = [
                'quarter' => $q,
                'label' => "Q{$q}",
                'hours' => round($quarterData->total_hours ?? 0, 2),
                'revenue' => round($quarterData->revenue ?? 0, 2),
                'tasks' => $quarterData->total_tasks ?? 0,
            ];
        }

        return $quarters;
    }

    public function getServiceEfficiency(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $stats = $this->getYearStats();

        $serviceHours = $stats['service_hours'];
        $billableHours = $stats['billable_hours'];
        $totalHours = $stats['total_hours'];

        $serviceToBillableRatio = $billableHours > 0 ? round($serviceHours / $billableHours, 2) : 0;
        $servicePercentage = $totalHours > 0 ? round(($serviceHours / $totalHours) * 100, 1) : 0;

        return [
            'service_hours' => $serviceHours,
            'billable_hours' => $billableHours,
            'service_to_billable_ratio' => $serviceToBillableRatio,
            'service_percentage' => $servicePercentage,
            'efficiency_score' => $totalHours > 0 ? round(($billableHours / $totalHours) * 100, 1) : 0,
        ];
    }

    public function getFixedVsHourlyComparison(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $fixedProjects = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->where('projects.is_fixed', 1)
            ->where('projects.is_internal', 0)
            ->select([
                DB::raw('COUNT(DISTINCT projects.id) as project_count'),
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('COUNT(tasks.id) as task_count'),
            ])
            ->first();

        $hourlyProjects = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at')
            ->where('projects.is_fixed', 0)
            ->where('projects.is_internal', 0)
            ->select([
                DB::raw('COUNT(DISTINCT projects.id) as project_count'),
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
                DB::raw('COUNT(tasks.id) as task_count'),
            ])
            ->first();

        return [
            'fixed' => [
                'project_count' => $fixedProjects->project_count ?? 0,
                'total_hours' => round($fixedProjects->total_hours ?? 0, 2),
                'task_count' => $fixedProjects->task_count ?? 0,
            ],
            'hourly' => [
                'project_count' => $hourlyProjects->project_count ?? 0,
                'total_hours' => round($hourlyProjects->total_hours ?? 0, 2),
                'revenue' => round($hourlyProjects->revenue ?? 0, 2),
                'task_count' => $hourlyProjects->task_count ?? 0,
            ],
        ];
    }

    public function getTaskCompletionPatterns(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $taskStats = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->select([
                DB::raw('AVG(minutes) / 60 as avg_task_duration'),
                DB::raw('MIN(minutes) / 60 as min_task_duration'),
                DB::raw('MAX(minutes) / 60 as max_task_duration'),
                DB::raw('COUNT(*) as total_tasks'),
                DB::raw('SUM(CASE WHEN minutes <= 30 THEN 1 ELSE 0 END) as quick_tasks'),
                DB::raw('SUM(CASE WHEN minutes > 30 AND minutes <= 120 THEN 1 ELSE 0 END) as medium_tasks'),
                DB::raw('SUM(CASE WHEN minutes > 120 THEN 1 ELSE 0 END) as long_tasks'),
            ])
            ->first();

        $totalTasks = $taskStats->total_tasks ?? 0;

        return [
            'avg_duration' => round($taskStats->avg_task_duration ?? 0, 2),
            'min_duration' => round($taskStats->min_task_duration ?? 0, 2),
            'max_duration' => round($taskStats->max_task_duration ?? 0, 2),
            'total_tasks' => $totalTasks,
            'quick_tasks' => $taskStats->quick_tasks ?? 0,
            'medium_tasks' => $taskStats->medium_tasks ?? 0,
            'long_tasks' => $taskStats->long_tasks ?? 0,
            'quick_percent' => $totalTasks > 0 ? round((($taskStats->quick_tasks ?? 0) / $totalTasks) * 100, 1) : 0,
            'medium_percent' => $totalTasks > 0 ? round((($taskStats->medium_tasks ?? 0) / $totalTasks) * 100, 1) : 0,
            'long_percent' => $totalTasks > 0 ? round((($taskStats->long_tasks ?? 0) / $totalTasks) * 100, 1) : 0,
        ];
    }

    public function getBusinessInsights(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $stats = $this->getYearStats();
        $revenueEfficiency = $this->getRevenueEfficiency();
        $growthTrends = $this->getGrowthTrends();
        $serviceEfficiency = $this->getServiceEfficiency();
        $clientValue = $this->getClientValueAnalysis();
        $projectProfitability = $this->getProjectProfitability();

        $insights = [];

        if ($revenueEfficiency['revenue_per_billable_hour'] > 0) {
            $insights[] = 'Average revenue per billable hour: '.CurrencyHelper::formatCurrency($revenueEfficiency['revenue_per_billable_hour']);
        }

        if ($revenueEfficiency['utilization_rate'] > 0) {
            $insights[] = "Utilization rate: {$revenueEfficiency['utilization_rate']}% (billable vs total hours)";
        }

        if ($growthTrends['has_prev_year_data']) {
            if ($growthTrends['revenue_growth'] > 0) {
                $insights[] = "Revenue growth: +{$growthTrends['revenue_growth']}% vs previous year";
            } elseif ($growthTrends['revenue_growth'] < 0) {
                $insights[] = "Revenue change: {$growthTrends['revenue_growth']}% vs previous year";
            }

            if ($growthTrends['hours_growth'] > 0) {
                $insights[] = "Hours worked: +{$growthTrends['hours_growth']}% vs previous year";
            }
        }

        if ($clientValue['revenue_concentration'] > 50) {
            $insights[] = "Top 3 clients represent {$clientValue['revenue_concentration']}% of revenue (consider diversification)";
        }

        if ($serviceEfficiency['service_to_billable_ratio'] > 0.3) {
            $insights[] = "Service work ratio: {$serviceEfficiency['service_percentage']}% (consider optimizing)";
        }

        if ($projectProfitability['avg_hourly_rate'] > 0) {
            $insights[] = 'Average hourly rate across projects: '.CurrencyHelper::formatCurrency($projectProfitability['avg_hourly_rate']);
        }

        if ($serviceEfficiency['efficiency_score'] < 60) {
            $insights[] = "Efficiency opportunity: Only {$serviceEfficiency['efficiency_score']}% of hours are billable";
        } elseif ($serviceEfficiency['efficiency_score'] > 80) {
            $insights[] = "Excellent efficiency: {$serviceEfficiency['efficiency_score']}% billable hours";
        }

        return $insights;
    }
}
