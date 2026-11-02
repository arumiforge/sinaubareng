<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

/**
 * Registered only in local and staging (AppServiceProvider), on the admin host.
 */
final class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    public function register(): void
    {
        // Secrets never reach Telescope, in any environment (LOG-4).
        Telescope::hideRequestParameters(['_token', 'password', 'password_confirmation', 'secret', 'token']);
        Telescope::hideRequestHeaders(['authorization', 'cookie', 'set-cookie', 'x-csrf-token', 'x-xsrf-token', 'x-goog-api-key']);

        $isLocal = $this->app->environment('local');

        Telescope::filter(fn (IncomingEntry $entry): bool => $isLocal
            || $entry->isReportableException()
            || $entry->isFailedRequest()
            || $entry->isFailedJob()
            || $entry->isScheduledTask()
            || $entry->hasMonitoredTag());
    }

    protected function gate(): void
    {
        Gate::define('viewTelescope', fn (mixed $user = null): bool => auth('staff')->check());
    }
}
