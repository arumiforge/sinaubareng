<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetSchoolContext;
use App\Http\Requests\Account\SwitchRoleRequest;
use App\Models\User;
use App\Tenancy\UserMemberships;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

/**
 * Writes the membership the user acts as into the session; `SetSchoolContext` reads it
 * on the next request.
 */
final class RoleSwitchController extends Controller
{
    public function update(SwitchRoleRequest $request, UserMemberships $memberships): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $membership = $memberships->findActive($user, $request->string('membership')->toString());

        if ($membership === null) {
            throw ValidationException::withMessages(['membership' => __('account.errors.role_unavailable')]);
        }

        $request->session()->put(SetSchoolContext::SESSION_KEY, $membership->id);

        return to_route('home')->with('toast', __('account.toasts.role_switched', [
            'role' => $membership->role->label(),
            'school' => $membership->school->name,
        ]));
    }
}
