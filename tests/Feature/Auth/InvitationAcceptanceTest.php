<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Http\Middleware\SetSchoolContext;
use App\Models\Invitation;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use Database\Factories\InvitationFactory;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

function acceptUrl(Invitation $invitation, string $token = InvitationFactory::TOKEN): string
{
    return route('invitations.accept', ['invitation' => $invitation->public_id, 'token' => $token]);
}

function acceptPost(Invitation $invitation): string
{
    return route('invitations.accept.store', ['invitation' => $invitation->public_id]);
}

/**
 * Every school-scoped model is read without a school here, so the invitation is made
 * for its school explicitly.
 */
function invitation(?School $school = null, MembershipRole $role = MembershipRole::Teacher): Invitation
{
    return Invitation::factory()->for($school ?? School::factory()->create())->role($role)->withToken()->create();
}

it('shows an open invitation to a guest', function (): void {
    $school = School::factory()->create(['name' => 'SD Negeri 2 Kudus']);
    $invitation = invitation($school);

    $this->get(acceptUrl($invitation))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/AcceptInvitation')
            ->where('invitation.school', 'SD Negeri 2 Kudus')
            ->where('invitation.email', $invitation->email)
            ->where('invitation.has_account', false)
            ->missing('invitation.token_hash'));
});

it('shows one neutral page for a wrong token, a used link, and an expired link', function (Closure $make, ?string $token): void {
    $invitation = $make();

    $this->get(acceptUrl($invitation, $token ?? InvitationFactory::TOKEN))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/InvitationUnavailable'));
})->with([
    'wrong token' => [fn () => invitation(), 'token-lain'],
    'accepted' => [fn () => Invitation::factory()->withToken()->accepted()->create(), null],
    'expired' => [fn () => Invitation::factory()->withToken()->expired()->create(), null],
]);

it('creates the account and membership, then signs in', function (): void {
    $school = School::factory()->create();
    $invitation = invitation($school);

    $this->post(acceptPost($invitation), [
        'token' => InvitationFactory::TOKEN,
        'name' => 'Siti Aminah, S.Pd.',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertRedirect(route('home'));

    $user = User::query()->where('email', $invitation->email)->sole();
    $membership = SchoolMembership::withoutSchoolScope()->whereBelongsTo($user)->sole();

    expect($user->name)->toBe('Siti Aminah, S.Pd.')
        ->and(Hash::check('rahasia123', $user->password))->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($membership->school_id)->toBe($school->id)
        ->and($membership->role)->toBe(MembershipRole::Teacher)
        ->and($membership->status)->toBe(MembershipStatus::Active)
        ->and($invitation->refresh()->accepted_at)->not->toBeNull();

    $this->assertAuthenticatedAs($user, 'web');
    expect(session(SetSchoolContext::SESSION_KEY))->toBe($membership->id);
});

it('validates a new account', function (): void {
    $invitation = invitation();

    $this->post(acceptPost($invitation), [
        'token' => InvitationFactory::TOKEN,
        'name' => '',
        'password' => 'abc',
        'password_confirmation' => 'xyz',
    ])->assertSessionHasErrors(['name', 'password']);

    expect($invitation->refresh()->accepted_at)->toBeNull();
    $this->assertGuest('web');
});

it('asks an existing account for its password and adds the role', function (): void {
    $user = User::factory()->teacherAt(School::factory()->create())->create();
    $school = School::factory()->create();
    $invitation = Invitation::factory()->for($school)->withToken()->create(['email' => $user->email]);

    $this->get(acceptUrl($invitation))
        ->assertInertia(fn (Assert $page) => $page->where('invitation.has_account', true));

    $this->post(acceptPost($invitation), ['token' => InvitationFactory::TOKEN, 'password' => 'salah'])
        ->assertSessionHasErrors(['password' => __('auth.invitation.wrong_password')]);

    $this->post(acceptPost($invitation), ['token' => InvitationFactory::TOKEN, 'password' => 'password'])
        ->assertRedirect(route('home'));

    expect(SchoolMembership::withoutSchoolScope()->whereBelongsTo($user)->active()->pluck('school_id')->all())
        ->toContain($school->id)
        ->toHaveCount(2);
    $this->assertAuthenticatedAs($user, 'web');
});

it('restores an ended membership instead of adding a second one', function (): void {
    $school = School::factory()->create();
    $user = User::factory()->create();
    $ended = SchoolMembership::factory()->for($school)->for($user)->ended()->create();
    $invitation = Invitation::factory()->for($school)->withToken()->create(['email' => $user->email]);

    $this->post(acceptPost($invitation), ['token' => InvitationFactory::TOKEN, 'password' => 'password'])
        ->assertRedirect(route('home'));

    expect($ended->refresh())->status->toBe(MembershipStatus::Active)->ended_on->toBeNull();
});

it('keeps one active principal per school', function (): void {
    $school = School::factory()->create();
    User::factory()->principalAt($school)->create();
    $invitation = invitation($school, MembershipRole::Principal);

    $this->post(acceptPost($invitation), [
        'token' => InvitationFactory::TOKEN,
        'name' => 'Kepala Baru',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors(['password' => __('auth.invitation.principal_taken')]);

    expect(User::query()->where('email', $invitation->email)->exists())->toBeFalse();
});

it('cannot be accepted twice or with a wrong token', function (): void {
    $invitation = Invitation::factory()->withToken()->accepted()->create();

    $this->post(acceptPost($invitation), ['token' => InvitationFactory::TOKEN, 'name' => 'X', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123'])
        ->assertRedirect(acceptUrl($invitation));

    $open = Invitation::factory()->withToken('token-undangan-kedua-0123456789')->create();
    $this->post(acceptPost($open), ['token' => 'token-lain', 'name' => 'X', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123'])
        ->assertRedirect(acceptUrl($open, 'token-lain'));

    expect($open->refresh()->accepted_at)->toBeNull()
        ->and(User::query()->where('email', $open->email)->exists())->toBeFalse();
});

it('warns a signed-in user that the invitation is for someone else', function (): void {
    $invitation = invitation();
    $other = User::factory()->create();

    actingAs($other)->get(acceptUrl($invitation))
        ->assertInertia(fn (Assert $page) => $page->where('signed_in_as', $other->email));
});
