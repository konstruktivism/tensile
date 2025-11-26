<?php

use App\Models\User;
use App\Models\Organisation;
use App\Models\Project;

it('can update Moneybird invoice', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create(['organisation_id' => $organisation->id]);

    $response = $this->actingAs($user)
        ->put("/moneybird/invoice/{$project->id}");

    $response->assertStatus(200);
});
