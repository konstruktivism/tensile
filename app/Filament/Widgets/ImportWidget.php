<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class ImportWidget extends Widget
{
    protected static string $view = 'filament.widgets.import-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getHeaderActions(): array
    {
        return [
            'title' => 'Import Actions',
        ];
    }

    public function import()
    {
        Artisan::call('command:daily-task');

        $output = Artisan::output();

        Notification::make()
            ->title('Success')
            ->body($output)
            ->success()
            ->send();
    }

    public function import30days()
    {
        Artisan::call('command:monthly-task');

        $output = Artisan::output();

        Notification::make()
            ->title('Success')
            ->body($output)
            ->success()
            ->send();
    }

    protected function getActions(): array
    {
        return [
            Action::make('import')
                ->label('Import')
                ->action('import'),

            Action::make('import-30days')
                ->label('Import 30 Days')
                ->action('import30days'),
        ];
    }
}
