<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Tenancy\SchoolContext;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
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

        // School accounts: at least 8 characters with letters and numbers (D-182).
        Password::defaults(fn (): Password => Password::min(8)->letters()->numbers());

        $this->configureRateLimits();
        $this->configurePasswordResetMail();
    }

    /**
     * Per-IP ceilings on the routes that work without a session. A whole school can
     * share one IP, so these are loose; sign-in also counts failures per email (LoginRequest).
     */
    private function configureRateLimits(): void
    {
        RateLimiter::for('login', fn (Request $request): Limit => Limit::perMinute(30)->by('login|'.$request->ip()));
        RateLimiter::for('password-reset', fn (Request $request): Limit => Limit::perMinute(5)->by('password-reset|'.$request->ip()));
        RateLimiter::for('invitation', fn (Request $request): Limit => Limit::perMinute(10)->by('invitation|'.$request->ip()));
    }

    /**
     * Indonesian reset mail in our own layout, instead of the framework's English one.
     */
    private function configurePasswordResetMail(): void
    {
        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            /** @var User $notifiable */
            $data = [
                'name' => $notifiable->name,
                'url' => route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]),
                'minutes' => config('auth.passwords.users.expire'),
            ];

            return (new MailMessage)
                ->subject(__('mail.password_reset.subject'))
                ->view(['mail.password-reset', 'mail.password-reset-text'], $data);
        });
    }
}
