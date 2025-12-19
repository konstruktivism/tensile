<?php

namespace App\Filament\Pages;

use App\Jobs\JobForecastImport;
use App\Models\Project;
use App\Models\Task;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class Settings extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 8;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public ?string $selectedMonthForImport = null;

    public function getMonthOptionsForImport(): array
    {
        return $this->getMonthOptionsForCurrentYear();
    }

    public function importTasksThisWeek(): void
    {
        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');

        Artisan::call('import:date-range', [
            'start_date' => $startOfWeek,
            'end_date' => $endOfWeek,
        ]);

        $output = Artisan::output();

        Notification::make()
            ->title('Week Import Complete')
            ->body($output)
            ->success()
            ->send();
    }

    public function importTasksThisMonth(): void
    {
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        Artisan::call('import:date-range', [
            'start_date' => $startOfMonth,
            'end_date' => $endOfMonth,
        ]);

        $output = Artisan::output();

        Notification::make()
            ->title('Month Import Complete')
            ->body($output)
            ->success()
            ->send();
    }

    public function importForecasts(): void
    {
        JobForecastImport::dispatch();

        Notification::make()
            ->title('Forecast Import Started')
            ->body('Forecast import has been queued and will process shortly.')
            ->success()
            ->send();
    }

    public function importForecastsFromWeek(): void
    {
        JobForecastImport::dispatch();

        Notification::make()
            ->title('Forecast Import Started')
            ->body('Importing forecasts from start of current week. This will catch events for newly added projects.')
            ->success()
            ->send();
    }

    protected function getWeekOptionsForCurrentYear(): array
    {
        $currentYear = Carbon::now()->year;
        $weeks = [];
        $startOfYear = Carbon::create($currentYear, 1, 1)->startOfWeek();
        $endOfYear = Carbon::create($currentYear, 12, 31)->endOfWeek();
        $currentDate = $startOfYear->copy();

        while ($currentDate->lte($endOfYear)) {
            $weekNumber = $currentDate->week;
            $weekStart = $currentDate->copy()->startOfWeek();
            $weekEnd = $currentDate->copy()->endOfWeek();
            $key = "{$currentYear}-W{$weekNumber}";
            $label = "Week {$weekNumber} ({$weekStart->format('M d')} - {$weekEnd->format('M d')})";
            $weeks[$key] = $label;
            $currentDate->addWeek();
        }

        return $weeks;
    }

    protected function getMonthOptionsForCurrentYear(): array
    {
        $currentYear = Carbon::now()->year;
        $months = [];
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

        for ($month = 1; $month <= 12; $month++) {
            $key = "{$currentYear}-{$month}";
            $label = "{$monthNames[$month]} {$currentYear}";
            $months[$key] = $label;
        }

        return $months;
    }

    public function importMissingTasksForMonth(): void
    {
        if (! $this->selectedMonthForImport) {
            Notification::make()
                ->title('No Month Selected')
                ->body('Please select a month to import missing tasks.')
                ->warning()
                ->send();

            return;
        }

        $googleCalendarService = app(GoogleCalendarService::class);
        $importedCount = 0;
        $checkedCount = 0;

        [$year, $month] = explode('-', $this->selectedMonthForImport);
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $startDate = $monthStart->format('Y-m-d');
        $endDate = $monthEnd->format('Y-m-d');

        $events = $googleCalendarService->getEventsByDateRange($startDate, $endDate, 1000);

        foreach ($events as $event) {
            $checkedCount++;
            $start = $event->start->dateTime ?? $event->start->date;
            $end = $event->end->dateTime ?? $event->end->date;

            if ($end && Carbon::parse($end)->isFuture()) {
                continue;
            }

            $completedAt = Carbon::parse($start)->startOfDay();
            $projectCode = substr(preg_replace('/[^A-Z]/', '', $event->getSummary()), 0, 3);
            $project = Project::where('project_code', $projectCode)->first();

            if ($project) {
                $existingTask = Task::where('icalUID', $event->iCalUID)
                    ->whereDate('completed_at', $completedAt->toDateString())
                    ->first();

                if (! $existingTask) {
                    $name = preg_replace('/[^a-zA-Z0-9\s.]/', '', substr($event->getSummary(), 4));
                    $name = preg_replace('/\/\S*/', '', $name);

                    Task::create([
                        'name' => $name,
                        'description' => $event->getDescription() ?? '',
                        'project_id' => $project->id,
                        'completed_at' => $completedAt,
                        'minutes' => isset($event->start->dateTime) && isset($event->end->dateTime) ? ceil((strtotime($event->end->dateTime) - strtotime($event->start->dateTime)) / 60) : 0,
                        'is_service' => str_contains($event->getSummary(), '🆓') ? 1 : 0,
                        'icalUID' => $event->iCalUID,
                    ]);

                    $importedCount++;
                }
            }
        }

        Notification::make()
            ->title('Import Complete')
            ->body("Checked {$checkedCount} events. Imported {$importedCount} missing task(s).")
            ->success()
            ->send();

        $this->selectedMonthForImport = null;
    }
}
