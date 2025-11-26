<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Reports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.reports';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?int $navigationSort = 2;

    public ?int $selectedYear = null;

    public ?int $selectedWeek = null;

    public function mount(): void
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
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

        return array_combine($weeks, array_map(fn ($w) => "Week {$w}", $weeks));
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
                    ->formatStateUsing(fn ($state) => "Week {$state}"),
                TextColumn::make('total_hours')
                    ->label('Total Hours')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2).'h'),
                TextColumn::make('billable_hours')
                    ->label('Billable Hours')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2).'h')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Year')
                    ->options($this->getYearOptions())
                    ->default(now()->year)
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            $this->selectedYear = $data['value'];
                            $this->selectedWeek = null;
                            $query->whereYear('tasks.completed_at', $data['value']);
                        }

                        return $query;
                    }),
                SelectFilter::make('week')
                    ->label('Week')
                    ->options(fn () => $this->getWeekOptions())
                    ->placeholder('All weeks')
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            $this->selectedWeek = $data['value'];
                            $year = $this->selectedYear ?? now()->year;
                            $week = $data['value'];

                            $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
                            $endOfWeek = $startOfWeek->copy()->endOfWeek();

                            $query->whereBetween('tasks.completed_at', [$startOfWeek, $endOfWeek]);
                        } else {
                            $this->selectedWeek = null;
                        }

                        return $query;
                    }),
            ])
            ->defaultSort('week', 'desc')
            ->paginated([10, 25, 50, 100])
            ->deferLoading();
    }

    protected function getTableQuery(): Builder
    {
        $year = $this->selectedYear ?? now()->year;

        $query = Task::query()
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->join('organisations', 'projects.organisation_id', '=', 'organisations.id')
            ->whereYear('tasks.completed_at', $year)
            ->whereNotNull('tasks.completed_at');

        return $query
            ->select([
                'projects.id as project_id',
                'projects.name as project_name',
                'organisations.name as organisation_name',
                DB::raw('WEEK(tasks.completed_at, 3) as week'),
                DB::raw('SUM(tasks.minutes) as total_minutes'),
                DB::raw('SUM(tasks.minutes) / 60 as total_hours'),
                DB::raw('SUM(CASE WHEN tasks.is_service = 0 AND projects.is_internal = 0 THEN tasks.minutes ELSE 0 END) / 60 as billable_hours'),
            ])
            ->groupBy('projects.id', 'projects.name', 'organisations.name', DB::raw('WEEK(tasks.completed_at, 3)'))
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
