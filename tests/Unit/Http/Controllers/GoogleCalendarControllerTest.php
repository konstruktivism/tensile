<?php

use App\Http\Controllers\GoogleCalendarController;
use App\Models\Project;
use App\Models\Task;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;

it('normalizes event timestamps consistently across imports', function () {
    $project = Project::factory()->create([
        'project_code' => 'ABC',
    ]);

    $eventStart = Carbon::now()->subDay()->setHour(14)->setMinute(30);
    $eventEnd = $eventStart->copy()->addHour();

    $event = new class($eventStart, $eventEnd)
    {
        public object $start;

        public object $end;

        public string $iCalUID = 'event-uid-123';

        public function __construct(private Carbon $startDateTime, private Carbon $endDateTime)
        {
            $this->start = (object) ['dateTime' => $this->startDateTime->toIso8601String()];
            $this->end = (object) ['dateTime' => $this->endDateTime->toIso8601String()];
        }

        public function getSummary(): string
        {
            return 'ABC Sample Task';
        }

        public function getDescription(): ?string
        {
            return 'Sample description';
        }
    };

    $service = \Mockery::mock(GoogleCalendarService::class);
    $service->shouldReceive('getEvents')
        ->once()
        ->with(32, 1, false)
        ->andReturn([$event]);
    $service->shouldReceive('getEvents')
        ->once()
        ->with(1000, 30)
        ->andReturn([$event]);

    $controller = new GoogleCalendarController($service);

    $controller->importEvents(false);
    $response = $controller->importLastMonth();

    expect(Task::count())->toBe(1);
    expect(Task::first()->completed_at->eq($eventStart->copy()->startOfDay()))->toBeTrue();
    expect($response->getData()->imported_count)->toBe(0);
});
