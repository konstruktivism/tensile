<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Carbon\Carbon;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Facades\Artisan;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = null;

    public static function getNavigationIcon(): ?string
    {
        return null;
    }

    protected ?string $heading = null;

    public function getHeading(): \Illuminate\Contracts\Support\Htmlable|string
    {
        return '';
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return null;
    }

    public function mount(): void
    {
        if (! is_array($this->filters)) {
            $this->filters = [];
        }

        if (! isset($this->filters['year'])) {
            $this->filters['year'] = now()->year;
        }
    }

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
        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');

        Artisan::call('import:date-range', [
            'start_date' => $startOfWeek,
            'end_date' => $endOfWeek,
        ]);

        $output = Artisan::output();

        Notification::make()
            ->title('Import Complete')
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
