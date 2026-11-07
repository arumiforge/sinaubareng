<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia as Assert;

function hostUrl(string $host, string $path = '/'): string
{
    return 'http://'.config("app.hosts.{$host}").$path;
}

it('serves the school app on app. with the web guard', function (): void {
    $this->get(hostUrl('app', '/masuk'))
        ->assertOk()
        ->assertViewIs('app')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
            ->where('translations', fn ($translations): bool => $translations['common.app_name'] === 'SIGRAK'
                && isset($translations['nav.items.today'])));

    expect(Auth::getDefaultDriver())->toBe('web');
});

it('serves the exam site on ujian. with the exam guard and only exam text', function (): void {
    $this->get(hostUrl('exam'))
        ->assertOk()
        ->assertViewIs('exam')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('translations', fn ($translations): bool => collect($translations)->keys()
                ->every(fn (string $key): bool => str_starts_with($key, 'common.') || str_starts_with($key, 'exam.'))));

    expect(Auth::getDefaultDriver())->toBe('exam');
});

it('serves the staff console on admin. with the staff guard', function (): void {
    $this->get(hostUrl('admin'))->assertRedirect(hostUrl('admin', '/masuk'));
    $this->get(hostUrl('admin', '/masuk'))->assertOk();

    expect(Filament::getPanel('admin')->getAuthGuard())->toBe('staff');
});

it('keeps each hostname to its own pages', function (): void {
    $this->get(hostUrl('exam', '/masuk'))->assertNotFound();
    $this->get(hostUrl('admin', '/horizon'))->assertForbidden();
});
