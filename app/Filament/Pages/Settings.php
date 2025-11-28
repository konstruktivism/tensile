<?php

namespace App\Filament\Pages;

use App\Jobs\JobForecastImport;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class Settings extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 8;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function importTasksThisWeek(): void
    {
        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');

        Artisan::call('import:date-range', [
            'start_date' => $startOfWeek,
            'end_date' => $endOfWeek,
        ]);

        $output = Artisan::output();

        Notification::make()
            ->title('Week Import Complete')
            ->body($output)
            ->success()
            ->send();
    }

    public function importTasksThisMonth(): void
    {
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        Artisan::call('import:date-range', [
            'start_date' => $startOfMonth,
            'end_date' => $endOfMonth,
        ]);

        $output = Artisan::output();

        Notification::make()
            ->title('Month Import Complete')
            ->body($output)
            ->success()
            ->send();
    }

    public function importForecasts(): void
    {
        JobForecastImport::dispatch();

        Notification::make()
            ->title('Forecast Import Started')
            ->body('Forecast import has been queued and will process shortly.')
            ->success()
            ->send();
    }
}
