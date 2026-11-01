<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Translation files always shared with the school app (LOC-9).
     *
     * @var list<string>
     */
    protected array $translationGroups = ['common', 'nav', 'terms', 'enums'];

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            // A closure, so partial reloads (`only: [...]`) skip it (LOC-11).
            'translations' => fn (): array => $this->translations(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function translations(): array
    {
        $groups = [];

        foreach ($this->translationGroups as $group) {
            $groups[$group] = trans($group);
        }

        // Empty groups flatten to `[]`; only strings reach the browser.
        return array_filter(Arr::dot($groups), is_string(...));
    }
}
