<?php

use App\Models\User;

it('can create a user', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->exists)->toBeTrue();
});

it('can make a request', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('can access login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
