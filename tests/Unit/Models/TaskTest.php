<?php

use App\Models\Task;
use App\Models\Project;
use App\Models\Organisation;
use Carbon\Carbon;

it('can create a task', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    $task = Task::factory()->create([
        'name' => 'Test Task',
        'project_id' => $project->id,
        'minutes' => 120,
        'completed_at' => Carbon::now(),
    ]);

    expect($task->name)->toBe('Test Task');
    expect($task->minutes)->toBe(120);
    expect($task->exists)->toBeTrue();
});

it('belongs to a project', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($task->project)->toBeInstanceOf(Project::class);
    expect($task->project->id)->toBe($project->id);
});

it('can calculate hours from minutes', function () {
    $task = Task::factory()->create(['minutes' => 120]);

    $hours = $task->minutes / 60;

    expect($hours)->toBe(2.0);
});

it('can check if task is a service', function () {
    $serviceTask = Task::factory()->create(['is_service' => true]);
    $regularTask = Task::factory()->create(['is_service' => false]);

    expect($serviceTask->is_service)->toBeTrue();
    expect($regularTask->is_service)->toBeFalse();
});

it('can check if task is invoiced', function () {
    $invoicedTask = Task::factory()->create(['invoiced' => true]);
    $nonInvoicedTask = Task::factory()->create(['invoiced' => false]);

    expect($invoicedTask->invoiced)->toBeTrue();
    expect($nonInvoicedTask->invoiced)->toBeFalse();
});

it('has completed_at as Carbon instance', function () {
    $task = Task::factory()->create(['completed_at' => Carbon::now()]);

    expect($task->completed_at)->toBeInstanceOf(Carbon::class);
});

it('can get week number from completed_at', function () {
    $task = Task::factory()->create(['completed_at' => Carbon::now()]);

    $weekNumber = $task->getCompletedAtWeekAttribute();

    expect($weekNumber)->toBe(Carbon::now()->format('W'));
});

it('has fillable attributes', function () {
    $task = new Task();
    $fillable = $task->getFillable();

    expect($fillable)->toContain('name', 'description', 'project_id', 'completed_at', 'minutes', 'icalUID', 'is_service', 'invoiced');
});

it('can have description as nullable', function () {
    $task = Task::factory()->create(['description' => null]);

    expect($task->description)->toBeNull();
});

it('can have icalUID for calendar integration', function () {
    $task = Task::factory()->create(['icalUID' => 'test-uid-123']);

    expect($task->icalUID)->toBe('test-uid-123');
});
