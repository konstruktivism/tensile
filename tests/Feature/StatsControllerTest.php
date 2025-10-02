<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Organisation;
use Carbon\Carbon;

it('can get hours per week stats', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    // Create some tasks for this week
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::now()->startOfWeek(),
        'minutes' => 120, // 2 hours
    ]);

    $response = $this->get('/api/hours-per-week');

    $response->assertStatus(200);
});

it('can get revenue per week stats', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    // Create some tasks for this week
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::now()->startOfWeek(),
        'minutes' => 120, // 2 hours
    ]);

    $response = $this->get('/api/revenue-per-week');

    $response->assertStatus(200);
});
