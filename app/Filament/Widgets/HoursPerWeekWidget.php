<?php

namespace App\Filament\Widgets;

use App\Http\Controllers\StatsController;
use Filament\Widgets\ChartWidget;

class HoursPerWeekWidget extends ChartWidget
{
    protected static ?string $heading = 'Hours Per Week';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        // Call the controller method directly instead of making HTTP requests
        $statsController = new StatsController();
        $response = $statsController->getHoursPerWeek();
        $data = $response->getData(true);

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
                    'backgroundColor' => 'rgba(75, 192, 192, 1)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 0,
                    'pointHitRadius' => 10, //
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        $option = [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'align' => 'start',
                ],
            ],
        ];

        return $option;
    }
}

class RevenueWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue per week';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 0;

    protected function getData(): array
    {
        // Call the controller method directly instead of making HTTP requests
        $statsController = new StatsController();
        $response = $statsController->getRevenuePerWeek();
        $data = $response->getData(true);

        $labels = array_map(fn($week) => "Week {$week}", array_keys($data));
        $revenue = array_values($data);

        return [
            'datasets' => [
                [
                    'label' => 'Revenue per Week',
                    'data' => $revenue,
                    'backgroundColor' => 'rgba(75, 192, 192, 1)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'cubicInterpolationMode' => 'monotone',
                    'borderWidth' => 4,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 10,
                    'pointBackgroundColor' => 'rgba(255, 159, 64, 1)',
                    'pointBorderColor' => 'rgba(255, 159, 64, 1)',
                    'pointHitRadius' => 10, //
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        $option = [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'align' => 'start',
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'intersect' => false,
            ],
        ];

        return $option;
    }
}

class ServicePercentageWidget extends ChartWidget
{
    protected static ?string $heading = 'Service Percentage';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Call the controller method directly instead of making HTTP requests
        $statsController = new StatsController();
        $response = $statsController->getHoursPerWeek();
        $data = $response->getData(true);

        $labels = array_map(fn($item) => "Week {$item['week']}", $data);
        $servicePercentage = array_map(fn($item) => round($item['service_percentage']), $data);
        $internalPercentage = array_map(fn($item) => round($item['internal_tasks']), $data);

        return [
            'datasets' => [
                [
                    'label' => 'Service',
                    'data' => $servicePercentage,
                    'backgroundColor' => 'rgba(255, 159, 64, 1)',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'borderWidth' => 1,
                    'stack' => 'Stack 0',
                    'pointHitRadius' => 10, //
                ],
                [
                    'label' => 'Internal',
                    'data' => $internalPercentage,
                    'backgroundColor' => 'rgba(255, 207, 0, 1)',
                    'borderColor' => 'rgba(255, 207, 0, 1)',
                    'borderWidth' => 1,
                    'stack' => 'Stack 0',
                    'pointHitRadius' => 10, //
                ],
                [
                    'label' => 'Paid',
                    'data' => array_map(fn($service, $internal) => round(100 - ($service + $internal)), $servicePercentage, $internalPercentage),
                    'backgroundColor' => 'rgba(54, 162, 235, 1)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'stack' => 'Stack 0',
                    'pointHitRadius' => 10, //
                ],
            ],
            'labels' => $labels,
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => false
                    ],
                ],
            ]
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        $option = [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'align' => 'start',
                ],
            ],
            'scales' => [
                'y' => [
                    'display' => false,
                ],
            ],
        ];

        return $option;
    }
}
