<?php

declare(strict_types=1);

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

// Only sign-in, password reset, and invitation acceptance work without a session (D-010).

/** Routes on app. that a guest may open. */
const GUEST_ROUTES = [
    'login', 'login.store',
    'password.request', 'password.email', 'password.reset', 'password.store',
    'invitations.accept', 'invitations.accept.store',
];

it('puts every other school app route behind sign-in', function (): void {
    $open = collect(Route::getRoutes()->getRoutes())
        ->filter(fn (RoutingRoute $route): bool => $route->getDomain() === config('app.hosts.app'))
        ->reject(fn (RoutingRoute $route): bool => in_array($route->getName(), GUEST_ROUTES, true))
        ->reject(fn (RoutingRoute $route): bool => in_array('auth', $route->gatherMiddleware(), true))
        ->map(fn (RoutingRoute $route): string => $route->getName() ?? $route->uri())
        ->values()
        ->all();

    expect($open)->toBe([]);
});

it('sends guests to the sign-in page', function (string $name): void {
    $this->get(route($name))->assertRedirect(route('login'));
})->with(['home', 'account.edit', 'teachers.index']);

it('refuses changes from guests', function (): void {
    $this->put(route('account.update'), ['name' => 'X'])->assertRedirect(route('login'));
    $this->post(route('logout'))->assertRedirect(route('login'));
});

it('keeps the guest pages open', function (string $name, array $parameters = []): void {
    $this->get(route($name, $parameters))->assertOk();
})->with([
    ['login'],
    ['password.request'],
    ['password.reset', ['token' => 'abc']],
    ['invitations.accept', ['invitation' => '0190f3a0-0000-7000-8000-000000000000']],
]);
