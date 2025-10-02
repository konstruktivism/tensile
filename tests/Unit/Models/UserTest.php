<?php

use App\Models\User;

it('can create a user', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
    expect($user->exists)->toBeTrue();
});

it('has projects relationship', function () {
    $user = User::factory()->create();

    expect($user->projects())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});

it('can be assigned to projects', function () {
    $user = User::factory()->create();
    $project = \App\Models\Project::factory()->create();

    $user->projects()->attach($project);

    expect($user->projects)->toHaveCount(1);
    expect($user->projects->first()->id)->toBe($project->id);
});

it('has tasks through projects', function () {
    $user = User::factory()->create();
    $project = \App\Models\Project::factory()->create();
    $user->projects()->attach($project);

    $task = \App\Models\Task::factory()->create(['project_id' => $project->id]);

    expect($user->projects->first()->tasks)->toHaveCount(1);
});

it('can have magic link fields', function () {
    $user = User::factory()->create([
        'magic_link_token' => 'test-token',
        'magic_link_expires_at' => now()->addMinutes(30),
    ]);

    expect($user->magic_link_token)->toBe('test-token');
    expect($user->magic_link_expires_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

it('can check if magic link is valid', function () {
    $user = User::factory()->create([
        'magic_link_token' => 'valid-token',
        'magic_link_expires_at' => now()->addMinutes(30),
    ]);

    expect($user->isMagicLinkValid('valid-token'))->toBeTrue();
    expect($user->isMagicLinkValid('invalid-token'))->toBeFalse();
});

it('can check if magic link is expired', function () {
    $user = User::factory()->create([
        'magic_link_token' => 'expired-token',
        'magic_link_expires_at' => now()->subMinutes(30),
    ]);

    expect($user->isMagicLinkValid('expired-token'))->toBeFalse();
});
