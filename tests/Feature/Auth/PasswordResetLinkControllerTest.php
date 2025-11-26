<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

it('can view forgot password page', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

it('can send password reset email', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHas('status', 'We have emailed your password reset link.');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('validates email for password reset', function () {
    $response = $this->post('/forgot-password', []);

    $response->assertSessionHasErrors('email');
});

it('handles non-existent email gracefully', function () {
    Notification::fake();

    $response = $this->post('/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHasErrors('email');

    // Should not send email for non-existent user
    Notification::assertNothingSent();
});

it('validates email format', function () {
    $response = $this->post('/forgot-password', [
        'email' => 'invalid-email',
    ]);

    $response->assertSessionHasErrors('email');
});

it('redirects authenticated user from forgot password page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/forgot-password');

    $response->assertRedirect('/projects');
});
