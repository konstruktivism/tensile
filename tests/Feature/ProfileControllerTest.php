<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('can view profile edit page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/profile');

    $response->assertStatus(200);
});

it('can update profile information', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $updateData = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ];

    $response = $this->actingAs($user)
        ->patch('/profile', $updateData);

    $response->assertRedirect('/profile');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

it('can update password via password route', function () {
    $user = User::factory()->create([
        'password' => bcrypt('old-password'),
    ]);

    $updateData = [
        'current_password' => 'old-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ];

    $response = $this->from('/profile')
        ->actingAs($user)
        ->put('/password', $updateData);

    $response->assertRedirect('/profile');

    // Verify the password was updated
    $user->refresh();
    expect(Hash::check('new-password', $user->password))->toBeTrue();
});

it('requires authentication to access profile', function () {
    $response = $this->get('/profile');

    $response->assertRedirect('/login');
});

it('can delete user account', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $deleteData = [
        'password' => 'password',
    ];

    $response = $this->actingAs($user)
        ->delete('/profile', $deleteData);

    $response->assertRedirect('/');

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
