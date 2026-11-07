<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds a page's own `lang/id` files to the shared `translations` prop:
 * `->middleware('translations:auth,account')` (LOC-9, LOC-10).
 */
final class ShareTranslations
{
    public const ATTRIBUTE = 'translation_groups';

    /** Files that hold server-only text and never reach the browser (LOC-10). */
    private const SERVER_ONLY = ['export', 'mail', 'notifications', 'validation', 'errors', 'console'];

    public function handle(Request $request, Closure $next, string ...$groups): Response
    {
        foreach ($groups as $group) {
            if (in_array($group, self::SERVER_ONLY, true)) {
                throw new InvalidArgumentException("The {$group} translations are server-only.");
            }
        }

        /** @var list<string> $current */
        $current = $request->attributes->get(self::ATTRIBUTE, []);
        $request->attributes->set(self::ATTRIBUTE, array_values(array_unique([...$current, ...$groups])));

        return $next($request);
    }
}
