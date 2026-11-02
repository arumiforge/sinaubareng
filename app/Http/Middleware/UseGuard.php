<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Makes the hostname's guard the default for the request (`web`, `exam`, `staff`).
 */
final class UseGuard
{
    public function handle(Request $request, Closure $next, string $guard): Response
    {
        Auth::shouldUse($guard);

        return $next($request);
    }
}
