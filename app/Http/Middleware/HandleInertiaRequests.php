<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\SchoolMembership;
use App\Models\User;
use App\Tenancy\UserMemberships;
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
            'translations' => fn (): array => $this->translations($request),
            'auth' => fn (): ?array => $this->auth($request),
            'flash' => fn (): array => ['toast' => $request->session()->get('toast')],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function translations(Request $request): array
    {
        /** @var list<string> $pageGroups */
        $pageGroups = $request->attributes->get(ShareTranslations::ATTRIBUTE, []);
        $groups = [];

        foreach ([...$this->translationGroups, ...$pageGroups] as $group) {
            $groups[$group] = trans($group);
        }

        // Empty groups flatten to `[]`; only strings reach the browser.
        return array_filter(Arr::dot($groups), is_string(...));
    }

    /**
     * The signed-in user and the roles the role switch offers. Public ids only (SV-5).
     *
     * @return array{user: array{name: string, email: string}, memberships: list<array{id: string, school: string, role: string, role_label: string, current: bool}>}|null
     */
    private function auth(Request $request): ?array
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return null;
        }

        $currentId = $request->session()->get(SetSchoolContext::SESSION_KEY);

        return [
            'user' => ['name' => $user->name, 'email' => $user->email],
            'memberships' => app(UserMemberships::class)->active($user)
                ->map(fn (SchoolMembership $membership): array => [
                    'id' => $membership->public_id,
                    'school' => $membership->school->name,
                    'role' => $membership->role->value,
                    'role_label' => $membership->role->label(),
                    'current' => $membership->id === $currentId,
                ])
                ->values()
                ->all(),
        ];
    }
}
