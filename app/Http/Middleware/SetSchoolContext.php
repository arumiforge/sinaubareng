<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Tenancy\Actions\ResolveSchoolContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the school context for a signed-in school user from the membership kept in
 * the session (the role switch writes it).
 */
final class SetSchoolContext
{
    public const SESSION_KEY = 'school_membership_id';

    public function __construct(private readonly ResolveSchoolContext $resolveSchoolContext) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User) {
            $preferred = $request->session()->get(self::SESSION_KEY);
            $membership = $this->resolveSchoolContext->handle($user, is_int($preferred) ? $preferred : null);

            $membership === null
                ? $request->session()->forget(self::SESSION_KEY)
                : $request->session()->put(self::SESSION_KEY, $membership->id);
        }

        return $next($request);
    }
}
