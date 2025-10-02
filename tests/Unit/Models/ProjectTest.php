<?php

use App\Models\Project;
use App\Models\Organisation;
use App\Models\User;
use App\Models\Task;

it('can create a project', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'name' => 'Test Project',
        'organisation_id' => $organisation->id,
        'hour_tariff' => 75.00,
    ]);

    expect($project->name)->toBe('Test Project');
    expect($project->hour_tariff)->toBe(75.00);
    expect($project->exists)->toBeTrue();
});

it('belongs to an organisation', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    expect($project->organisation)->toBeInstanceOf(Organisation::class);
    expect($project->organisation->id)->toBe($organisation->id);
});

it('has many tasks', function () {
    $project = Project::factory()->create();
    $tasks = Task::factory()->count(3)->create(['project_id' => $project->id]);

    expect($project->tasks)->toHaveCount(3);
    expect($project->tasks()->count())->toBe(3);
});

it('has many users', function () {
    $project = Project::factory()->create();
    $users = User::factory()->count(2)->create();

    foreach ($users as $user) {
        $project->users()->attach($user);
    }

    expect($project->users)->toHaveCount(2);
    expect($project->users()->count())->toBe(2);
});

it('can calculate total hours from tasks', function () {
    $project = Project::factory()->create();

    Task::factory()->create([
        'project_id' => $project->id,
        'minutes' => 120, // 2 hours
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'minutes' => 180, // 3 hours
    ]);

    $totalMinutes = $project->tasks()->sum('minutes');
    $totalHours = $totalMinutes / 60;

    expect($totalHours)->toBe(5.0);
});

it('can check if notifications are enabled', function () {
    $projectWithNotifications = Project::factory()->create(['notifications' => true]);
    $projectWithoutNotifications = Project::factory()->create(['notifications' => false]);

    expect($projectWithNotifications->notifications)->toBeTrue();
    expect($projectWithoutNotifications->notifications)->toBeFalse();
});

it('can check if project is fixed price', function () {
    $fixedProject = Project::factory()->create(['is_fixed' => true]);
    $hourlyProject = Project::factory()->create(['is_fixed' => false]);

    expect($fixedProject->is_fixed)->toBeTrue();
    expect($hourlyProject->is_fixed)->toBeFalse();
});

it('can check if project is internal', function () {
    $internalProject = Project::factory()->create(['is_internal' => true]);
    $externalProject = Project::factory()->create(['is_internal' => false]);

    expect($internalProject->is_internal)->toBeTrue();
    expect($externalProject->is_internal)->toBeFalse();
});

it('has fillable attributes', function () {
    $project = new Project();
    $fillable = $project->getFillable();

    expect($fillable)->toContain('name', 'description', 'organisation_id', 'hour_tariff', 'is_fixed', 'notifications', 'is_internal');
});
