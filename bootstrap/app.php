<?php

declare(strict_types=1);

use App\Http\Middleware\HandleExamInertiaRequests;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetSchoolContext;
use App\Http\Middleware\ShareTranslations;
use App\Http\Middleware\UseGuard;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // One route file per hostname, each with its own guard (D-048).
        then: function (): void {
            Route::domain(config('app.hosts.app'))
                ->middleware(['web', UseGuard::class.':web', SetSchoolContext::class, HandleInertiaRequests::class])
                ->group(base_path('routes/app.php'));

            Route::domain(config('app.hosts.exam'))
                ->middleware(['web', UseGuard::class.':exam', HandleExamInertiaRequests::class])
                ->name('exam.')
                ->group(base_path('routes/exam.php'));

            Route::domain(config('app.hosts.admin'))
                ->middleware(['web', UseGuard::class.':staff'])
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['translations' => ShareTranslations::class]);

        // The hostname's guard is chosen before `auth`, and the school context is set
        // before route model binding, so a school-owned {model} resolves through the
        // school scope and another school's row is a 404 (TEN-4).
        $middleware->prependToPriorityList(AuthenticatesRequests::class, UseGuard::class);
        $middleware->prependToPriorityList(SubstituteBindings::class, SetSchoolContext::class);

        // Only the school app has these routes; the staff console redirects on its own.
        $middleware->redirectGuestsTo(fn (): string => route('login'));
        $middleware->redirectUsersTo(fn (): string => route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
