<?php

declare(strict_types=1);

namespace App\Providers;

use App\Tenancy\SchoolContext;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as TelescopeBaseServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Fresh for every request and every queued job, so a school never leaks into the next one.
        $this->app->scoped(SchoolContext::class);

        // Telescope never runs in production (LOG-4).
        if ($this->app->environment('local', 'staging') && class_exists(TelescopeBaseServiceProvider::class)) {
            $this->app->register(TelescopeBaseServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        // Indonesian URL verbs: /dokumen/buat, /dokumen/{document}/ubah (D-149).
        Route::resourceVerbs(['create' => 'buat', 'edit' => 'ubah']);

        Date::use(CarbonImmutable::class);
        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
