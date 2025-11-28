<?php

namespace App\Filament\Pages;

use App\Models\ForecastTask;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Forecast extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.forecast';

    protected static ?string $navigationLabel = 'Forecast';

    protected static ?int $navigationSort = 3;

    public ?int $selectedYear = null;

    public function mount(): void
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
    }

    public function updatedSelectedYear(): void
    {
        $this->resetTable();
    }

    public function getYearOptions(): array
    {
        $currentYear = now()->year;
        $currentWeekStart = Carbon::now()->startOfWeek();

        // Get years from forecast data
        $dataYears = ForecastTask::query()
            ->whereNull('deleted_at')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', $currentWeekStart)
            ->get()
            ->map(fn ($task) => Carbon::parse($task->scheduled_at)->year)
            ->unique()
            ->toArray();

        // Always show from 2025 onwards, or current year onwards if current year is before 2025
        $startYear = max(2025, $currentYear);
        $endYear = max($startYear, $currentYear + 1);

        // Include any years from data that are beyond our range
        if (! empty($dataYears)) {
            $maxDataYear = max($dataYears);
            $endYear = max($endYear, $maxDataYear);
        }

        $years = [];
        // Start with current year first
        $years[] = $currentYear;

        // Add other years (excluding current year if already added)
        for ($year = $startYear; $year <= $endYear; $year++) {
            if ($year !== $currentYear && ! in_array($year, $years)) {
                $years[] = $year;
            }
        }

        // Sort descending so current year is first
        rsort($years);

        return array_combine($years, $years);
    }

    public function getMonthOptions(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $currentWeekStart = Carbon::now()->startOfWeek();

        $months = ForecastTask::query()
            ->whereNull('deleted_at')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', $currentWeekStart)
            ->whereYear('scheduled_at', $year)
            ->get()
            ->map(fn ($task) => Carbon::parse($task->scheduled_at)->month)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        if (empty($months)) {
            // Show current month and next 2 months if no data
            $currentMonth = now()->month;
            $months = [];
            for ($i = 0; $i < 3; $i++) {
                $month = $currentMonth + $i;
                if ($month > 12) {
                    $month = $month - 12;
                }
                $months[] = $month;
            }
        }

        return array_combine($months, array_map(fn ($m) => Carbon::create()->month($m)->format('F'), $months));
    }

    public function getMonthlyTable(int $month): Table
    {
        return $this->buildTable($this->getMonthlyTableQuery($month));
    }

    public function table(Table $table): Table
    {
        return $table->query(ForecastTask::query()->whereRaw('1 = 0'));
    }

    protected function buildTable(Builder $query): Table
    {
        return Table::make($this)
            ->query($query)
            ->columns([
                TextColumn::make('week')
                    ->label('Week')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "Week {$state}"),
                TextColumn::make('project_name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organisation_name')
                    ->label('Organisation')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('planned_hours')
                    ->label('Planned Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('h')
                    ->sortable()
                    ->summarize(Sum::make()->label('Month Total')->formatStateUsing(fn ($state) => number_format($state, 2).'h')),
                TextColumn::make('billable_hours')
                    ->label('Billable Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('h')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->summarize(Sum::make()->label('Month Total')->formatStateUsing(fn ($state) => number_format($state, 2).'h')),
                TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->label('Month Total')->money('EUR')),
            ])
            ->defaultSort('week', 'asc')
            ->paginated(false)
            ->deferLoading();
    }

    protected function getMonthlyTableQuery(int $month): Builder
    {
        $now = Carbon::now();
        $currentWeekStart = $now->copy()->startOfWeek();
        $year = $this->selectedYear ?? now()->year;
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $query = ForecastTask::query()
            ->whereNull('forecast_tasks.deleted_at')
            ->join('projects', 'forecast_tasks.project_id', '=', 'projects.id')
            ->join('organisations', 'projects.organisation_id', '=', 'organisations.id')
            ->whereNotNull('forecast_tasks.scheduled_at')
            ->where('forecast_tasks.scheduled_at', '>=', $currentWeekStart)
            ->whereYear('forecast_tasks.scheduled_at', $year)
            ->whereMonth('forecast_tasks.scheduled_at', $month);

        return $query
            ->select([
                'projects.id as project_id',
                'projects.name as project_name',
                'projects.hour_tariff',
                'organisations.name as organisation_name',
                DB::raw('WEEK(forecast_tasks.scheduled_at, 3) as week'),
                DB::raw('SUM(forecast_tasks.minutes) as total_minutes'),
                DB::raw('SUM(forecast_tasks.minutes) / 60 as planned_hours'),
                DB::raw('SUM(CASE WHEN forecast_tasks.is_service = 0 AND projects.is_internal = 0 THEN forecast_tasks.minutes ELSE 0 END) / 60 as billable_hours'),
                DB::raw('SUM(CASE WHEN forecast_tasks.is_service = 0 AND projects.is_internal = 0 THEN forecast_tasks.minutes ELSE 0 END) / 60 * projects.hour_tariff as revenue'),
            ])
            ->groupBy('projects.id', 'projects.name', 'projects.hour_tariff', 'organisations.name', DB::raw('WEEK(forecast_tasks.scheduled_at, 3)'))
            ->havingRaw('MIN(forecast_tasks.scheduled_at) >= ?', [$currentWeekStart])
            ->orderBy('week', 'asc')
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
