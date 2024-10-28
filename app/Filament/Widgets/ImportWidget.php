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
        $this->executeCommand('command:daily-task');
    }

    public function importWeeks()
    {
        $this->executeCommand('import:weeks');
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
                ->label('Import')
                ->action('import'),

            Action::make('importWeeks')
                ->label('Import Last 30 Days')
                ->action('importWeeks'),
        ];
    }
}
