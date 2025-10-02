<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('can view forgot password page', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

it('can send password reset email', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect('/forgot-password');
    $response->assertSessionHas('status', 'We have emailed your password reset link.');

    Mail::assertSent(\Illuminate\Auth\Notifications\ResetPassword::class);
});

it('validates email for password reset', function () {
    $response = $this->post('/forgot-password', []);

    $response->assertSessionHasErrors('email');
});

it('handles non-existent email gracefully', function () {
    Mail::fake();

    $response = $this->post('/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertRedirect('/forgot-password');
    $response->assertSessionHas('status', 'We have emailed your password reset link.');

    // Should not send email for non-existent user
    Mail::assertNotSent(\Illuminate\Auth\Notifications\ResetPassword::class);
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

    $response->assertRedirect('/dashboard');
});
