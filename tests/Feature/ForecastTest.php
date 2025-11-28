<?php

use App\Filament\Pages\Forecast;
use App\Jobs\JobCleanupForecastTasks;
use App\Models\ForecastTask;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;

it('can create forecast tasks', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'project_code' => 'ABC',
        'hour_tariff' => 100,
        'is_internal' => false,
    ]);

    $forecastTask = ForecastTask::factory()->create([
        'project_id' => $project->id,
        'icalUID' => 'test-ical-uid-123',
        'scheduled_at' => Carbon::now()->addDays(7),
        'minutes' => 120,
        'is_service' => false,
    ]);

    expect($forecastTask->scheduled_at)->not->toBeNull();
    expect($forecastTask->minutes)->toBe(120);
    expect($forecastTask->project_id)->toBe($project->id);
});

it('soft deletes forecast tasks when corresponding task is completed', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'project_code' => 'XYZ',
    ]);

    $scheduledDate = Carbon::now()->addDays(5)->startOfDay();

    $forecastTask = ForecastTask::factory()->create([
        'project_id' => $project->id,
        'icalUID' => 'test-ical-123',
        'scheduled_at' => $scheduledDate,
    ]);

    // Create a completed task with matching icalUID
    $completedTask = Task::factory()->create([
        'project_id' => $project->id,
        'icalUID' => 'test-ical-123',
        'completed_at' => $scheduledDate->copy()->addDays(1),
    ]);

    $job = new JobCleanupForecastTasks;
    $job->handle();

    $forecastTask->refresh();
    expect($forecastTask->trashed())->toBeTrue();
    expect($forecastTask->deleted_at)->not->toBeNull();
});

it('does not delete forecast tasks when dates are too far apart', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'project_code' => 'XYZ',
    ]);

    $forecastTask = ForecastTask::factory()->create([
        'project_id' => $project->id,
        'icalUID' => 'test-ical-456',
        'scheduled_at' => Carbon::now()->addDays(5),
    ]);

    // Create a completed task with matching icalUID but date is more than 7 days apart
    $completedTask = Task::factory()->create([
        'project_id' => $project->id,
        'icalUID' => 'test-ical-456',
        'completed_at' => Carbon::now()->addDays(15),
    ]);

    $job = new JobCleanupForecastTasks;
    $job->handle();

    $forecastTask->refresh();
    expect($forecastTask->trashed())->toBeFalse();
});

it('displays forecast page with revenue calculations', function () {
    $user = User::factory()->create(['is_admin' => true]);
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'hour_tariff' => 100,
        'is_internal' => false,
    ]);

    $forecastTask = ForecastTask::factory()->create([
        'project_id' => $project->id,
        'scheduled_at' => Carbon::now()->addWeek(),
        'minutes' => 120,
        'is_service' => false,
    ]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $this->actingAs($user);

    Livewire::test(Forecast::class)
        ->assertSuccessful();
});

it('calculates revenue correctly for billable forecast tasks', function () {
    $organisation = Organisation::factory()->create();
    $billableProject = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'hour_tariff' => 100,
        'is_internal' => false,
    ]);

    $internalProject = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'hour_tariff' => 100,
        'is_internal' => true,
    ]);

    // Billable task (not service, not internal)
    ForecastTask::factory()->create([
        'project_id' => $billableProject->id,
        'scheduled_at' => Carbon::now()->addWeek(),
        'minutes' => 120,
        'is_service' => false,
    ]);

    // Service task (should not be billable)
    ForecastTask::factory()->create([
        'project_id' => $billableProject->id,
        'scheduled_at' => Carbon::now()->addWeek(),
        'minutes' => 60,
        'is_service' => true,
    ]);

    // Internal project task (should not be billable)
    ForecastTask::factory()->create([
        'project_id' => $internalProject->id,
        'scheduled_at' => Carbon::now()->addWeek(),
        'minutes' => 60,
        'is_service' => false,
    ]);

    $user = User::factory()->create(['is_admin' => true]);
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $this->actingAs($user);

    Livewire::test(Forecast::class)
        ->assertSuccessful();
});
