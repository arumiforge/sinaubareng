<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

final class PasswordController extends Controller
{
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // A new remember token signs out "remember me" on other devices.
        $user->forceFill([
            'password' => $request->string('password')->toString(),
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();

        return to_route('account.edit')->with('toast', __('account.toasts.password_saved'));
    }
}
