<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Filament\Panel;

class Dashboard extends \Filament\Pages\Dashboard
{

    protected static ?string $navigationIcon = '';

    protected function getHeaderActions(): array
    {
        return [
//            Action::make('import')
//                ->label('Import')
//                ->action(function () {
//                    Artisan::call('command:daily-task');
//
//                    $output = Artisan::output();
//
//                    Notification::make()
//                        ->title('Success')
//                        ->body($output)
//                        ->success()
//                        ->send();
//                }),
//
//            Action::make('import-30days')
//                ->label('Import 30 Days')
//                ->action(function () {
//                    Artisan::call('command:monthly-task');
//
//                    $output = Artisan::output();
//
//                    Notification::make()
//                        ->title('Success')
//                        ->body($output)
//                        ->success()
//                        ->send();
//                }),
        ];
    }
}
