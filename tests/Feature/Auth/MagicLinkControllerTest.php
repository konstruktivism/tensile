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

    $response->assertStatus(200);

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

    $response = $this->get('/magic-login?token=valid-token');

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

it('cannot login with expired magic link', function () {
    $user = User::factory()->create([
        'magic_link_token' => 'expired-token',
        'magic_link_expires_at' => now()->subMinutes(30),
    ]);

    $response = $this->get('/magic-login?token=expired-token');

    $response->assertRedirect('/login/magic');
    $this->assertGuest();
});

it('cannot login with invalid magic link token', function () {
    $response = $this->get('/magic-login?token=invalid-token');

    $response->assertRedirect('/login/magic');
    $this->assertGuest();
});
