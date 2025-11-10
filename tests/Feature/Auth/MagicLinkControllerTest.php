<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('can view magic link page', function () {
    $response = $this->get('/login/magic');

    $response->assertStatus(200);
});

it('can send magic link email', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/login/magic', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Magic link sent! Please check your email and close this window.');

    Mail::assertSent(\App\Mail\MagicLinkMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('validates email for magic link', function () {
    $response = $this->post('/login/magic', []);

    $response->assertSessionHasErrors('email');
});

it('can login with valid magic link', function () {
    $user = User::factory()->create([
        'magic_link_token' => 'valid-token',
        'magic_link_expires_at' => now()->addMinutes(30),
    ]);

    $response = $this->get(sprintf('/magic-login?token=%s&email=%s', 'valid-token', $user->email));

    $response->assertRedirect(route('projects', absolute: false));
    $this->assertAuthenticatedAs($user);
});

it('cannot login with expired magic link', function () {
    $user = User::factory()->create([
        'magic_link_token' => 'expired-token',
        'magic_link_expires_at' => now()->subMinutes(30),
    ]);

    $response = $this->get(sprintf('/magic-login?token=%s&email=%s', 'expired-token', $user->email));

    $response->assertRedirect('/login');
    $this->assertGuest();
});

it('cannot login with invalid magic link token', function () {
    $user = User::factory()->create([
        'magic_link_token' => 'valid-token',
        'magic_link_expires_at' => now()->addMinutes(30),
    ]);

    $response = $this->get(sprintf('/magic-login?token=%s&email=%s', 'invalid-token', $user->email));

    $response->assertRedirect('/login');
    $this->assertGuest();
});
