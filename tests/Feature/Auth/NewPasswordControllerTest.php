<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

it('can view reset password page', function () {
    $response = $this->get('/reset-password/token123');

    $response->assertStatus(200);
});

it('can reset password with valid token', function () {
    $user = User::factory()->create([
        'password' => bcrypt('old-password'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertRedirect('/login');

    // Verify password was updated
    $user->refresh();
    expect(Hash::check('new-password', $user->password))->toBeTrue();
});

it('validates reset password form data', function () {
    $response = $this->post('/reset-password', []);

    $response->assertSessionHasErrors(['token', 'email', 'password']);
});

it('validates password confirmation', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors('password');
});

it('cannot reset password with invalid token', function () {
    $user = User::factory()->create();

    $response = $this->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertSessionHasErrors('email');
});

it('cannot reset password with wrong email', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'wrong@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertSessionHasErrors('email');
});

it('validates password minimum length', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
});
