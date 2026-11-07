<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;

it('mails a reset link in Indonesian', function (): void {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertRedirect(route('login'))
        ->assertSessionHas('status', __('auth.reset.link_sent'));

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
        $mail = $notification->toMail($user);
        $html = (string) $mail->render();

        return $mail->subject === __('mail.password_reset.subject')
            && str_contains($html, route('password.reset', ['token' => $notification->token, 'email' => $user->email]));
    });
});

it('answers the same for an unknown email', function (): void {
    Notification::fake();

    $this->post(route('password.email'), ['email' => 'tidak-ada@example.com'])
        ->assertRedirect(route('login'))
        ->assertSessionHas('status', __('auth.reset.link_sent'))
        ->assertSessionHasNoErrors();

    Notification::assertNothingSent();
});

it('validates the email', function (): void {
    $this->post(route('password.email'), ['email' => ''])->assertSessionHasErrors('email');
});

it('throttles reset requests per IP', function (): void {
    Notification::fake();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('password.email'), ['email' => "orang{$attempt}@example.com"]);
    }

    $this->post(route('password.email'), ['email' => 'orang6@example.com'])->assertTooManyRequests();
});

it('shows the new password form', function (): void {
    $this->get(route('password.reset', ['token' => 'abc', 'email' => 'guru@example.com']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/ResetPassword')
            ->where('token', 'abc')
            ->where('email', 'guru@example.com'));
});

it('sets a new password with a valid token', function (): void {
    $user = User::factory()->create();
    $token = Password::broker('users')->createToken($user);

    $this->post(route('password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'sandiBaru123',
        'password_confirmation' => 'sandiBaru123',
    ])->assertRedirect(route('login'))->assertSessionHas('status', __('auth.reset.done'));

    expect(Hash::check('sandiBaru123', $user->refresh()->password))->toBeTrue();
});

it('refuses a wrong token and a weak password', function (): void {
    $user = User::factory()->create();

    $this->post(route('password.store'), [
        'token' => 'salah',
        'email' => $user->email,
        'password' => 'sandiBaru123',
        'password_confirmation' => 'sandiBaru123',
    ])->assertSessionHasErrors(['email' => __('auth.reset.invalid')]);

    $this->post(route('password.store'), [
        'token' => Password::broker('users')->createToken($user),
        'email' => $user->email,
        'password' => 'pendek',
        'password_confirmation' => 'pendek',
    ])->assertSessionHasErrors('password');

    expect(Hash::check('password', $user->refresh()->password))->toBeTrue();
});
