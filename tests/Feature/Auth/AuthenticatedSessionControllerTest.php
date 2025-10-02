<?php

use App\Models\User;

it('can view login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('cannot login with non-existent email', function () {
    $response = $this->post('/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('validates login form data', function () {
    $response = $this->post('/login', []);

    $response->assertSessionHasErrors(['email', 'password']);
});

it('can logout authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/logout');

    $response->assertRedirect('/');
    $this->assertGuest();
});

it('redirects authenticated user from login page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/login');

    $response->assertRedirect('/dashboard');
});

it('remembers user login when requested', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'remember' => true,
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);

    // Check that remember token was set
    $user->refresh();
    expect($user->remember_token)->not->toBeNull();
});
