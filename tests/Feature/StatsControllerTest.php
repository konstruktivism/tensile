<?php

use App\Models\Organisation;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
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

it('does not sum weeks from different years in hours per week stats', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    $testYear = 2024;
    $otherYear = 2023;

    // Create tasks from 2023 (should be filtered out when querying 2024)
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::create($otherYear, 6, 15),
        'minutes' => 300, // 5 hours
    ]);

    // Create tasks from 2024
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::create($testYear, 6, 15),
        'minutes' => 120, // 2 hours
    ]);

    // Query for 2024 data
    $response = $this->get("/api/hours-per-week/{$testYear}");
    $response->assertStatus(200);

    $data = $response->json();

    // Calculate total minutes for 2024
    $totalMinutes2024 = collect($data)->sum('total_minutes');
    expect($totalMinutes2024)->toBe(120);

    // Verify 2023 data is separate - query for 2023
    $response2023 = $this->get("/api/hours-per-week/{$otherYear}");
    $response2023->assertStatus(200);
    $data2023 = $response2023->json();

    // Calculate total minutes for 2023
    $totalMinutes2023 = collect($data2023)->sum('total_minutes');
    expect($totalMinutes2023)->toBe(300);
});

it('does not sum weeks from different years in revenue per week stats', function () {
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'is_internal' => false,
        'hour_tariff' => 100,
    ]);

    $testYear = 2024;
    $otherYear = 2023;

    // Create tasks from 2023 (should be filtered out when querying 2024)
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::create($otherYear, 6, 15),
        'minutes' => 300, // 5 hours = 500 revenue
        'is_service' => false,
    ]);

    // Create tasks from 2024
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::create($testYear, 6, 15),
        'minutes' => 120, // 2 hours = 200 revenue
        'is_service' => false,
    ]);

    // Query for 2024 data
    $response = $this->get("/api/revenue-per-week/{$testYear}");
    $response->assertStatus(200);

    $data = $response->json();

    // Calculate total revenue for 2024
    $totalRevenue2024 = array_sum($data);
    expect($totalRevenue2024)->toBe(200);

    // Verify 2023 data is separate
    $response2023 = $this->get("/api/revenue-per-week/{$otherYear}");
    $response2023->assertStatus(200);
    $data2023 = $response2023->json();
    $totalRevenue2023 = array_sum($data2023);
    expect($totalRevenue2023)->toBe(500);
});
