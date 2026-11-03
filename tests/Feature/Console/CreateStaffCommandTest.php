<?php

declare(strict_types=1);

use App\Enums\StaffRole;
use App\Models\StaffUser;
use Illuminate\Support\Facades\Hash;

it('creates the first admin', function (): void {
    $this->artisan('staff:create', ['email' => 'admin@example.com', '--name' => 'Admin SIGRAK'])
        ->expectsQuestion('Password (min. 12 characters)', 'kata-sandi-panjang')
        ->assertSuccessful();

    $staff = StaffUser::query()->where('email', 'admin@example.com')->sole();
    expect($staff->role)->toBe(StaffRole::Admin)
        ->and(Hash::check('kata-sandi-panjang', $staff->password))->toBeTrue();
});

it('refuses a short password or an unknown role', function (string $role, string $password): void {
    $this->artisan('staff:create', ['email' => 'x@example.com', '--name' => 'X', '--role' => $role])
        ->expectsQuestion('Password (min. 12 characters)', $password)
        ->assertFailed();

    expect(StaffUser::query()->count())->toBe(0);
})->with([
    'short password' => ['admin', 'pendek'],
    'unknown role' => ['owner', 'kata-sandi-panjang'],
]);
