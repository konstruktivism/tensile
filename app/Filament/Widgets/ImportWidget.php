<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class ImportWidget extends Widget
{
    protected static ?int $sort = 0;

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
        $this->executeCommand('command:daily-task');
    }

    public function importWeeks()
    {
        $this->executeCommand('import:weeks');
    }

    public function importSeptember2025()
    {
        Artisan::call('import:date-range', [
            'start_date' => '2025-09-01',
            'end_date' => '2025-09-30'
        ]);

        $output = Artisan::output();

        Notification::make()
            ->title('September 2025 Import')
            ->body($output)
            ->success()
            ->send();
    }

    protected function executeCommand(string $command): void
    {
        Artisan::call($command);

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
                ->label('Import Yesterday')
                ->action('import'),

            Action::make('importWeeks')
                ->label('Import Last 30 Days')
                ->action('importWeeks'),

            Action::make('importSeptember2025')
                ->label('Import September 2025')
                ->action('importSeptember2025')
                ->color('info'),
        ];
    }
}
