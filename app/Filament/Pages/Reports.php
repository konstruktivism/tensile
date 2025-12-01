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

    public $selectedMonth = null;

    public string $reportType = 'week';

    public function mount(): void
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
    }

    public function updatedSelectedYear(): void
    {
        $this->selectedWeek = null;
        $this->selectedMonth = null;
        $this->resetTable();
    }

    public function updatedSelectedWeek(): void
    {
        if ($this->selectedWeek !== null && $this->selectedWeek !== '') {
            $this->selectedMonth = null;
        }
        if ($this->selectedWeek === '') {
            $this->selectedWeek = null;
        }
        $this->resetTable();
    }

    public function updatedSelectedMonth($value): void
    {
        if ($value === '' || $value === null) {
            $this->selectedMonth = null;
        } else {
            $this->selectedMonth = (int) $value;
        }
        if ($this->selectedMonth !== null) {
            $this->selectedWeek = null;
        }
        $this->resetTable();
    }

    public function updatedReportType(): void
    {
        $this->selectedWeek = null;
        $this->selectedMonth = null;
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

    public function getMonthOptions(): array
    {
        $year = $this->selectedYear ?? now()->year;
        $months = Task::query()
            ->whereYear('completed_at', $year)
            ->whereNotNull('completed_at')
            ->selectRaw('MONTH(completed_at) as month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month')
            ->toArray();

        if (empty($months)) {
            $months = [now()->month];
        }

        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        return array_combine($months, array_map(fn($m) => $monthNames[$m] ?? "Month {$m}", $months));
    }

    public function table(Table $table): Table
    {
        $isMonthView = $this->reportType === 'month';

        $columns = [
            TextColumn::make('project_name')
                ->label('Project')
                ->searchable()
                ->sortable(),
            TextColumn::make('organisation_name')
                ->label('Organisation')
                ->searchable()
                ->sortable(),
        ];

        if ($isMonthView) {
            $columns[] = TextColumn::make('month')
                ->label('Month')
                ->sortable()
                ->formatStateUsing(function ($state) {
                    $monthNames = [
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ];

                    return $monthNames[$state] ?? "Month {$state}";
                });
        } else {
            $columns[] = TextColumn::make('week')
                ->label('Week')
                ->sortable()
                ->formatStateUsing(fn($state) => "Week {$state}");
        }

        $columns = array_merge($columns, [
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
        ]);

        return $table
            ->query($this->getTableQuery())
            ->columns($columns)
            ->defaultSort($isMonthView ? 'month' : 'week', 'desc')
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

        $isMonthView = $this->reportType === 'month';

        if ($isMonthView && $this->selectedMonth !== null && $this->selectedMonth !== '') {
            $query->whereMonth('tasks.completed_at', (int) $this->selectedMonth);
        } elseif (! $isMonthView && $this->selectedWeek !== null && $this->selectedWeek !== '') {
            $startOfWeek = Carbon::now()->setISODate($year, $this->selectedWeek)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            $query->whereBetween('tasks.completed_at', [$startOfWeek, $endOfWeek]);
        }

        $selectFields = [
            'projects.id as project_id',
            'projects.name as project_name',
            'organisations.name as organisation_name',
            'projects.hour_tariff',
            'projects.is_fixed',
            DB::raw('SUM(tasks.minutes) as total_minutes'),
            DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
            DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 THEN tasks.minutes ELSE 0 END) / 60 as billable_hours'),
            DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 AND projects.is_fixed = 0 THEN tasks.minutes / 60 * projects.hour_tariff ELSE 0 END) as revenue'),
        ];

        $groupByFields = [
            'projects.id',
            'projects.name',
            'organisations.name',
            'projects.hour_tariff',
            'projects.is_fixed',
        ];

        if ($isMonthView) {
            $selectFields[] = DB::raw('MONTH(tasks.completed_at) as month');
            $groupByFields[] = DB::raw('MONTH(tasks.completed_at)');
            $orderByField = 'month';
        } else {
            $selectFields[] = DB::raw('WEEK(tasks.completed_at, 3) as week');
            $groupByFields[] = DB::raw('WEEK(tasks.completed_at, 3)');
            $orderByField = 'week';
        }

        return $query
            ->select($selectFields)
            ->groupBy($groupByFields)
            ->orderBy($orderByField, 'desc')
            ->orderBy('projects.name');
    }

    public function getTableRecordKey($record): string
    {
        $periodKey = $this->reportType === 'month'
            ? ($record->month ?? '')
            : ($record->week ?? '');

        return sprintf(
            '%s-%s',
            $record->project_id ?? '',
            $periodKey
        );
    }
}
