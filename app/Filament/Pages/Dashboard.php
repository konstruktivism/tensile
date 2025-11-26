<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Facades\Artisan;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = '';

    public function getYearOptions(): array
    {
        $availableYears = Task::query()
            ->whereNotNull('completed_at')
            ->selectRaw('YEAR(completed_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // If no data exists, default to current year
        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return array_combine($availableYears, $availableYears);
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                View::make('filament.components.year-filter-and-import')
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public function importWeeks(): void
    {
        Artisan::call('import:weeks');

        $output = Artisan::output();

        Notification::make()
            ->title('Success')
            ->body($output)
            ->success()
            ->send();
    }

    public function getColumns(): int|string|array
    {
        return 12;
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
