<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Akun: the signed-in user's own profile. Email changes go through support for now,
 * because the email is the sign-in name and the invitation address.
 */
final class AccountController extends Controller
{
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        return Inertia::render('Account/Edit', [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'nip' => $user->nip,
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->update($request->validated());

        return to_route('account.edit')->with('toast', __('account.toasts.profile_saved'));
    }
}
