<?php

namespace App\Filament\Widgets;

use App\Http\Controllers\StatsController;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ServicePercentageWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Service Percentage';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $year = $this->filters['year'] ?? now()->year;

        // Call the controller method directly instead of making HTTP requests
        $statsController = new StatsController;
        $response = $statsController->getHoursPerWeek($year);
        $data = $response->getData(true);

        $labels = array_map(fn($item) => $item['week'], $data);
        $servicePercentage = array_map(fn($item) => round($item['service_percentage']), $data);
        $internalPercentage = array_map(fn($item) => round($item['internal_tasks']), $data);

        return [
            'datasets' => [
                [
                    'label' => 'Service',
                    'data' => $servicePercentage,
                    'backgroundColor' => 'rgba(250, 204, 21, 1)',
                    'borderColor' => 'rgba(250, 204, 21, 1)',
                    'borderWidth' => 1,
                    'stack' => 'Stack 0',
                    'pointHitRadius' => 10,
                ],
                [
                    'label' => 'Internal',
                    'data' => $internalPercentage,
                    'backgroundColor' => 'rgba(250, 204, 21, 0.6)',
                    'borderColor' => 'rgba(250, 204, 21, 0.6)',
                    'borderWidth' => 1,
                    'stack' => 'Stack 0',
                    'pointHitRadius' => 10,
                ],
                [
                    'label' => 'Paid',
                    'data' => array_map(fn($service, $internal) => round(100 - ($service + $internal)), $servicePercentage, $internalPercentage),
                    'backgroundColor' => 'rgba(250, 204, 21, 0.3)',
                    'borderColor' => 'rgba(250, 204, 21, 0.3)',
                    'borderWidth' => 1,
                    'stack' => 'Stack 0',
                    'pointHitRadius' => 10,
                ],
            ],
            'labels' => $labels,
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            plugins: {
                legend: {
                    display: true,
                    align: 'start',
                },
            },
            scales: {
                y: {
                    display: false,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000) return (value / 1000) + 'K';
                            return value;
                        }
                    }
                }
            }
        }
        JS);
    }
}
