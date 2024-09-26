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
        ];
    }
}
