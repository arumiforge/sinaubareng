<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Enums\MembershipRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreInvitationRequest;
use App\Models\Invitation;
use App\Models\School;
use App\Models\User;
use App\Tenancy\Actions\InviteMember;
use App\Tenancy\SchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * The operator invites teachers. Route binding goes through the school scope, so
 * another school's invitation is a 404 (TEN-4).
 */
final class InvitationController extends Controller
{
    public function store(StoreInvitationRequest $request, InviteMember $inviteMember, SchoolContext $context): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $school = School::query()->findOrFail($context->schoolId());
        $email = $request->string('email')->toString();

        $inviteMember->handle($school, $email, MembershipRole::Teacher, $user);

        return to_route('teachers.index')->with('toast', __('school.toasts.invitation_sent', ['email' => $email]));
    }

    public function resend(Request $request, Invitation $invitation, InviteMember $inviteMember): RedirectResponse
    {
        Gate::authorize('update', $invitation);

        /** @var User $user */
        $user = $request->user();
        $inviteMember->resend($invitation, $user);

        return to_route('teachers.index')->with('toast', __('school.toasts.invitation_sent', ['email' => $invitation->email]));
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        Gate::authorize('delete', $invitation);

        $invitation->delete();

        return to_route('teachers.index')->with('toast', __('school.toasts.invitation_cancelled', ['email' => $invitation->email]));
    }
}
