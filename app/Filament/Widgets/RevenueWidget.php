<?php

namespace App\Filament\Widgets;

use App\Http\Controllers\StatsController;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RevenueWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Revenue per week';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $year = $this->filters['year'] ?? now()->year;

        // Call the controller method directly instead of making HTTP requests
        $statsController = new StatsController;
        $response = $statsController->getRevenuePerWeek($year);
        $data = $response->getData(true);

        $labels = array_keys($data);
        $revenue = array_values($data);

        return [
            'datasets' => [
                [
                    'label' => 'Revenue per Week',
                    'data' => $revenue,
                    'backgroundColor' => 'rgba(250, 204, 21, 0.3)',
                    'borderColor' => 'rgba(250, 204, 21, 1)',
                    'cubicInterpolationMode' => 'monotone',
                    'borderWidth' => 3,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 10,
                    'pointBackgroundColor' => 'rgba(250, 204, 21, 1)',
                    'pointBorderColor' => 'rgba(250, 204, 21, 1)',
                    'pointHitRadius' => 10,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
            interaction: {
                mode: 'nearest',
                intersect: false,
            },
            scales: {
                y: {
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
