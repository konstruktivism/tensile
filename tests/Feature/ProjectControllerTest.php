<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Organisation;

it('can view projects index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/projects');

    $response->assertStatus(200);
});

it('can view a specific project', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    $response = $this->actingAs($user)
        ->get("/project/{$project->id}");

    $response->assertStatus(200);
});

it('requires authentication to view projects', function () {
    $response = $this->get('/projects');

    $response->assertRedirect('/login');
});

it('can view project week view', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    $response = $this->actingAs($user)
        ->get("/project/{$project->id}/1");

    $response->assertStatus(200);
});
