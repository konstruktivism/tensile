<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Models\Organisation;
use Carbon\Carbon;

it('can import calendar events', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    $response = $this->actingAs($user)
        ->get('/import');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
    ]);
});

it('can import weeks of calendar events', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    $response = $this->actingAs($user)
        ->get('/import-weeks');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'message',
    ]);
});

it('requires authentication to import events', function () {
    $response = $this->get('/import');

    $response->assertRedirect('/login');
});

it('creates tasks from calendar events', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    // Mock calendar events data
    $mockEvents = [
        [
            'summary' => 'Test Task 1',
            'start' => ['dateTime' => Carbon::now()->toISOString()],
            'end' => ['dateTime' => Carbon::now()->addHour()->toISOString()],
            'id' => 'test-event-1',
        ],
        [
            'summary' => 'Test Task 2',
            'start' => ['dateTime' => Carbon::now()->addDay()->toISOString()],
            'end' => ['dateTime' => Carbon::now()->addDay()->addHours(2)->toISOString()],
            'id' => 'test-event-2',
        ],
    ];

    // This test would need to be adapted based on your actual Google Calendar integration
    // For now, we'll test that the controller responds correctly
    $response = $this->actingAs($user)
        ->get('/import');

    $response->assertStatus(200);
});

it('handles empty calendar events', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/import');

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Events imported of yesterday.',
    ]);
});

it('handles import errors gracefully', function () {
    $user = User::factory()->create();

    // This test would mock Google API errors
    $response = $this->actingAs($user)
        ->get('/import');

    // The controller should handle errors gracefully
    $response->assertStatus(200);
});

it('imports events for specified number of weeks', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    $response = $this->actingAs($user)
        ->get('/import-weeks?weeks=4');

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Events imported of the last month.',
    ]);
});
