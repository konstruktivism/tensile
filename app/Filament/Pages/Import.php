<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class Import extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.import';

    protected static ?string $navigationLabel = 'Import';

    protected static ?int $navigationSort = 3;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importThisWeek')
                ->label('Import this week')
                ->action(fn () => $this->importThisWeek()),

            Action::make('importThisMonth')
                ->label('Import this month')
                ->action(fn () => $this->importThisMonth()),
        ];
    }

    public function importThisWeek(): void
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

    public function importThisMonth(): void
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
}
