<?php

declare(strict_types=1);

use App\Http\Middleware\HandleExamInertiaRequests;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\UseGuard;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // One route file per hostname, each with its own guard (D-048).
        then: function (): void {
            Route::domain(config('app.hosts.app'))
                ->middleware(['web', UseGuard::class.':web', HandleInertiaRequests::class])
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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
