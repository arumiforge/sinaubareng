<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

/**
 * Horizon lives on the admin host behind the `staff` guard (D-055).
 */
final class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn (mixed $user = null): bool => auth('staff')->check());
    }
}
