<?php

declare(strict_types=1);

use App\Http\Middleware\SetSchoolContext;
use App\Models\School;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

it('shows the sign-in page with its text', function (): void {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
            ->where('translations', fn ($translations): bool => $translations['auth.titles.login'] === 'Masuk'
                && ! isset($translations['mail.footer'])));
});

it('signs in with the right password', function (): void {
    $user = User::factory()->teacherAt(School::factory()->create())->create();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user, 'web');
});

it('rejects a wrong password with an Indonesian message', function (): void {
    $user = User::factory()->create();

    $this->from(route('login'))
        ->post(route('login.store'), ['email' => $user->email, 'password' => 'salah'])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    $this->assertGuest('web');
});

it('validates the sign-in form', function (): void {
    $this->post(route('login.store'), ['email' => 'bukan-email', 'password' => ''])
        ->assertSessionHasErrors(['email', 'password']);
});

it('locks an email after five failed attempts', function (): void {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'salah']);
    }

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => __('auth.throttle', ['minutes' => 1])]);

    $this->assertGuest('web');
});

it('sends signed-in users away from the sign-in page', function (): void {
    actingAs(User::factory()->create())->get(route('login'))->assertRedirect(route('home'));
});

it('signs out and forgets the chosen role', function (): void {
    $user = User::factory()->teacherAt(School::factory()->create())->create();

    actingAs($user)->post(route('logout'))
        ->assertRedirect(route('login'))
        ->assertSessionMissing(SetSchoolContext::SESSION_KEY);

    $this->assertGuest('web');
});

it('never signs staff in to the school app', function (): void {
    $this->post(route('login.store'), ['email' => 'staf@example.com', 'password' => 'password'])
        ->assertSessionHasErrors('email');
});
