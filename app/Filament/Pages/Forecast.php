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

    protected static ?string $navigationLabel = 'Forecasts';

    protected static ?int $navigationSort = 5;

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

        // Get years from forecast data
        $dataYears = ForecastTask::query()
            ->whereNull('deleted_at')
            ->whereNotNull('scheduled_at')
            ->get()
            ->map(fn ($task) => Carbon::parse($task->scheduled_at)->year)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $years = [];

        // If we have data years, use them
        if (! empty($dataYears)) {
            $years = $dataYears;
        } else {
            // Fallback: show current year and next year if no data
            $years = [$currentYear, $currentYear + 1];
        }

        // Always include current year if not already present
        if (! in_array($currentYear, $years)) {
            $years[] = $currentYear;
        }

        // Sort descending so current year is first
        rsort($years);

        return array_combine($years, $years);
    }

    public function getMonthOptions(): array
    {
        $year = $this->selectedYear ?? now()->year;

        $months = ForecastTask::query()
            ->whereNull('deleted_at')
            ->whereNotNull('scheduled_at')
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
        $year = $this->selectedYear ?? now()->year;

        $query = ForecastTask::query()
            ->whereNull('forecast_tasks.deleted_at')
            ->join('projects', 'forecast_tasks.project_id', '=', 'projects.id')
            ->join('organisations', 'projects.organisation_id', '=', 'organisations.id')
            ->whereNotNull('forecast_tasks.scheduled_at')
            ->where('forecast_tasks.scheduled_at', '>=', Carbon::now()->startOfDay())
            ->whereYear('forecast_tasks.scheduled_at', $year)
            ->whereMonth('forecast_tasks.scheduled_at', $month)
            ->whereRaw('YEARWEEK(forecast_tasks.scheduled_at, 3) DIV 100 = ?', [$year]);

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
