<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Http;

class HoursPerWeekWidget extends ChartWidget
{
    protected static ?string $heading = 'Hours Per Week';

    protected int | string | array $columnSpan = 'full';


    protected function getData(): array
    {
        // Fetch data from your API
        $response = Http::get(config('app.url') . '/api/hours-per-week');
        $data = $response->json();

        // Process data for the chart
        $labels = array_map(fn($item) => "Week {$item['week']}", $data);
        $hours = array_map(fn($item) => $item['total_minutes'] / 60, $data);
        $totalTasks = array_map(fn($item) => $item['total_tasks'], $data);
        $servicePercentage = array_map(fn($item) => $item['service_percentage'], $data);

        return [
            'datasets' => [
                [
                    'label' => 'Hours per Week',
                    'data' => $hours,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

class TotalTasksWidget extends ChartWidget
{
    protected static ?string $heading = 'Total Tasks';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $response = Http::get(config('app.url') . '/api/hours-per-week');
        $data = $response->json();

        $labels = array_map(fn($item) => "Week {$item['week']}", $data);
        $totalTasks = array_map(fn($item) => $item['total_tasks'], $data);

        return [
            'datasets' => [
                [
                    'label' => 'Total Tasks',
                    'data' => $totalTasks,
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

class ServicePercentageWidget extends ChartWidget
{
    protected static ?string $heading = 'Service Percentage';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $response = Http::get(config('app.url') . '/api/hours-per-week');
        $data = $response->json();

        $labels = array_map(fn($item) => "Week {$item['week']}", $data);
        $servicePercentage = array_map(fn($item) => $item['service_percentage'], $data);

        return [
            'datasets' => [
                [
                    'label' => 'Service Percentage',
                    'data' => $servicePercentage,
                    'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Total Percentage',
                    'data' => array_map(fn($percentage) => 100 - $percentage, $servicePercentage),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
