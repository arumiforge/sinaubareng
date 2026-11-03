<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StaffRole;
use Database\Factories\StaffUserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Internal Tim SIGRAK account for the staff console, on its own `staff` guard
 * and table so a school account can never become staff (data-model.md).
 * The `staff_users` table is created with staff sign-in in slice 1.
 */
/**
 * @property StaffRole $role
 */
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
final class StaffUser extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<StaffUserFactory> */
    use HasFactory;

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin';
    }

    public function hasStaffRole(StaffRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => StaffRole::class,
        ];
    }
}
