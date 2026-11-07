<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

final class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    /**
     * Answers the same whether or not the address has an account, so the form cannot be
     * used to find out who uses Sigrak.
     */
    public function store(PasswordResetLinkRequest $request): RedirectResponse
    {
        Password::broker('users')->sendResetLink($request->only('email'));

        return to_route('login')->with('status', __('auth.reset.link_sent'));
    }
}
