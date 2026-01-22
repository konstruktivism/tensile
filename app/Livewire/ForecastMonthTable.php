<?php

namespace App\Livewire;

use App\Models\ForecastTask;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ForecastMonthTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function makeFilamentTranslatableContentDriver(): ?\Filament\Support\Contracts\TranslatableContentDriver
    {
        return null;
    }

    public int $month;

    public int $year;

    public function mount(int $month, int $year): void
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
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
                    ->alignRight()
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->label('')
                            ->formatStateUsing(fn ($state) => number_format($state, 2).'h'),
                    ]),
                TextColumn::make('billable_hours')
                    ->label('Billable Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('h')
                    ->alignRight()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->summarize([
                        Sum::make()
                            ->label('')
                            ->formatStateUsing(fn ($state) => number_format($state, 2).'h'),
                    ]),
                TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->label('')
                            ->money('EUR'),
                    ]),
            ])
            ->defaultSort('week', 'asc')
            ->defaultGroup('week')
            ->groups([
                \Filament\Tables\Grouping\Group::make('week')
                    ->label('Week')
                    ->collapsible()
                    ->scopeQueryByKeyUsing(fn (Builder $query, string $key) => $query->whereRaw('WEEK(forecast_tasks.scheduled_at, 3) = ?', [$key])),
            ])
            ->paginated(false)
            ->deferLoading();
    }

    protected function getTableQuery(): Builder
    {
        $query = ForecastTask::query()
            ->whereNull('forecast_tasks.deleted_at')
            ->join('projects', 'forecast_tasks.project_id', '=', 'projects.id')
            ->join('organisations', 'projects.organisation_id', '=', 'organisations.id')
            ->whereNotNull('forecast_tasks.scheduled_at')
            ->where('forecast_tasks.scheduled_at', '>=', Carbon::now()->startOfDay())
            ->whereYear('forecast_tasks.scheduled_at', $this->year)
            ->whereMonth('forecast_tasks.scheduled_at', $this->month)
            ->whereRaw('YEARWEEK(forecast_tasks.scheduled_at, 3) DIV 100 = ?', [$this->year]);

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
        $projectId = $record->project_id ?? '';
        $week = $record->week ?? '';
        $month = $this->month ?? '';

        return sprintf(
            '%s-%s-%s',
            $projectId ?: '0',
            $week ?: '0',
            $month ?: '0'
        );
    }

    public function render()
    {
        return view('livewire.forecast-month-table');
    }
}
