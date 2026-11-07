<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetSchoolContext;
use App\Http\Requests\Auth\AcceptInvitationRequest;
use App\Models\User;
use App\Tenancy\Actions\AcceptInvitation;
use App\Tenancy\Actions\FindInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Works with or without a session: the token in the mailed link authorizes it (D-010).
 */
final class InvitationAcceptanceController extends Controller
{
    public function show(Request $request, string $invitation, FindInvitation $findInvitation): Response
    {
        $token = $request->string('token')->toString();
        $found = $findInvitation->handle($invitation, $token);

        if ($found === null || ! $found->isPending()) {
            // One page for unknown, used, and expired links, so a link reveals nothing.
            return Inertia::render('Auth/InvitationUnavailable');
        }

        $signedIn = $request->user();

        return Inertia::render('Auth/AcceptInvitation', [
            'invitation' => [
                'id' => $found->public_id,
                'token' => $token,
                'email' => $found->email,
                'school' => $found->school->name,
                'role' => $found->role->label(),
                'has_account' => User::query()->where('email', $found->email)->exists(),
            ],
            'signed_in_as' => $signedIn instanceof User && $signedIn->email !== $found->email ? $signedIn->email : null,
        ]);
    }

    public function store(AcceptInvitationRequest $request, string $invitation, AcceptInvitation $acceptInvitation): RedirectResponse
    {
        $found = $request->invitation();

        if ($found === null || ! $found->isPending()) {
            return to_route('invitations.accept', ['invitation' => $invitation, 'token' => $request->string('token')->toString()]);
        }

        /** @var array{name?: string|null, password: string} $input */
        $input = $request->safe()->only(['name', 'password']);
        $membership = $acceptInvitation->handle($found, $input);

        // Accepting signs in as the invited account, replacing any other session.
        Auth::guard('web')->loginUsingId($membership->user_id);
        $request->session()->regenerate();
        $request->session()->put(SetSchoolContext::SESSION_KEY, $membership->id);

        return to_route('home')->with('toast', __('auth.invitation.accepted', ['school' => $found->school->name]));
    }
}
