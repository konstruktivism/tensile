<?php

namespace App\Filament\Pages;

use App\Helpers\CurrencyHelper;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Reports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.reports';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?int $navigationSort = 6;

    public ?int $selectedYear = null;

    public ?int $selectedWeek = null;

    public function mount(): void
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
    }

    public function updatedSelectedYear(): void
    {
        $this->selectedWeek = null;
        $this->resetTable();
    }

    public function updatedSelectedWeek(): void
    {
        $this->resetTable();
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

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return array_combine($availableYears, $availableYears);
    }

    public function getWeekOptions(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $weeks = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->selectRaw('WEEK(completed_at, 3) as week')
            ->distinct()
            ->orderBy('week', 'desc')
            ->pluck('week')
            ->toArray();

        if (empty($weeks)) {
            $weeks = [now()->week];
        }

        return array_combine($weeks, array_map(fn($w) => "Week {$w}", $weeks));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('project_name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organisation_name')
                    ->label('Organisation')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('week')
                    ->label('Week')
                    ->sortable()
                    ->formatStateUsing(fn($state) => "Week {$state}"),
                TextColumn::make('total_hours')
                    ->label('Total Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('h')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')->formatStateUsing(fn($state) => number_format($state, 2) . 'h')),
                TextColumn::make('billable_hours')
                    ->label('Billable Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('h')
                    ->sortable()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->summarize(Sum::make()->label('Total')->formatStateUsing(fn($state) => number_format($state, 2) . 'h')),
                TextColumn::make('revenue')
                    ->label('Revenue')
                    ->sortable()
                    ->color(fn($state, $record) => ($state > 0 && $record->is_fixed == 0) ? 'success' : 'gray')
                    ->formatStateUsing(fn($state, $record) => $record->is_fixed == 0 ? CurrencyHelper::formatCurrency($state) : '-')
                    ->summarize(Sum::make()->label('Total')->formatStateUsing(fn($state) => CurrencyHelper::formatCurrency($state))),
            ])
            ->defaultSort('week', 'desc')
            ->paginated([10, 25, 50, 100])
            ->deferLoading();
    }

    protected function getTableQuery(): Builder
    {
        $query = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->join('organisations', 'projects.organisation_id', '=', 'organisations.id')
            ->whereNotNull('tasks.completed_at');

        $year = $this->selectedYear ?? now()->year;
        $query->whereYear('tasks.completed_at', $year);

        if ($this->selectedWeek !== null) {
            $startOfWeek = Carbon::now()->setISODate($year, $this->selectedWeek)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            $query->whereBetween('tasks.completed_at', [$startOfWeek, $endOfWeek]);
        }

        return $query
            ->select([
                'projects.id as project_id',
                'projects.name as project_name',
                'organisations.name as organisation_name',
                'projects.hour_tariff',
                'projects.is_fixed',
                DB::raw('WEEK(tasks.completed_at, 3) as week'),
                DB::raw('SUM(tasks.minutes) as total_minutes'),
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 THEN tasks.minutes ELSE 0 END) / 60 as billable_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
            ])
            ->groupBy('projects.id', 'projects.name', 'organisations.name', 'projects.hour_tariff', 'projects.is_fixed', DB::raw('WEEK(tasks.completed_at, 3)'))
            ->orderBy('week', 'desc')
            ->orderBy('projects.name');
    }

    public function getTableRecordKey($record): string
    {
        return sprintf(
            '%s-%s',
            $record->project_id ?? '',
            $record->week ?? ''
        );
    }
}
