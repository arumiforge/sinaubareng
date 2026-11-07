<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    Mail::fake();
});

function operatorOf(School $school): User
{
    return User::factory()->operatorAt($school)->create();
}

it('lists the school\'s teachers and open invitations, nothing from other schools', function (): void {
    $school = School::factory()->create();
    $teacher = User::factory()->teacherAt($school)->create();
    Invitation::factory()->for($school)->create(['email' => 'baru@example.com']);
    Invitation::factory()->for($school)->accepted()->create();
    $other = School::factory()->create();
    User::factory()->teacherAt($other)->create();
    Invitation::factory()->for($other)->create(['email' => 'sekolah-lain@example.com']);

    actingAs(operatorOf($school))->get(route('teachers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('School/Teachers')
            ->has('teachers', 1)
            ->where('teachers.0.email', $teacher->email)
            ->has('invitations', 1)
            ->where('invitations.0.email', 'baru@example.com')
            ->where('translations', fn ($translations): bool => isset($translations['school.titles.teachers'])));
});

it('keeps the Guru page to operators', function (MembershipRole $role): void {
    $user = User::factory()->memberAt(School::factory()->create(), $role)->create();

    actingAs($user)->get(route('teachers.index'))->assertForbidden();
    actingAs($user)->post(route('invitations.store'), ['email' => 'guru@example.com'])->assertForbidden();
})->with([MembershipRole::Teacher, MembershipRole::Principal]);

it('forbids a user without any school', function (): void {
    actingAs(User::factory()->create())->get(route('teachers.index'))->assertForbidden();
});

it('invites a teacher by email', function (): void {
    $school = School::factory()->create(['name' => 'SD Negeri 3 Jepara']);
    $operator = operatorOf($school);

    actingAs($operator)->post(route('invitations.store'), ['email' => 'Guru.Baru@Example.com'])
        ->assertRedirect(route('teachers.index'))
        ->assertSessionHas('toast');

    $invitation = Invitation::withoutSchoolScope()->sole();
    expect($invitation)
        ->school_id->toBe($school->id)
        ->email->toBe('guru.baru@example.com')
        ->role->toBe(MembershipRole::Teacher)
        ->invited_by->toBe($operator->id);

    Mail::assertQueued(InvitationMail::class, function (InvitationMail $mail) use ($invitation): bool {
        $html = $mail->render();

        return $mail->hasTo('guru.baru@example.com')
            && $mail->schoolName === 'SD Negeri 3 Jepara'
            && str_contains($mail->url, $invitation->public_id)
            && str_contains($html, 'SD Negeri 3 Jepara')
            && hash_equals($invitation->getAttribute('token_hash'), Invitation::hashToken((string) str($mail->url)->after('token=')));
    });
});

it('validates the email and refuses an existing teacher', function (): void {
    $school = School::factory()->create();
    $teacher = User::factory()->teacherAt($school)->create();
    $operator = operatorOf($school);

    actingAs($operator)->post(route('invitations.store'), ['email' => 'bukan-email'])->assertSessionHasErrors('email');
    actingAs($operator)->post(route('invitations.store'), ['email' => $teacher->email])
        ->assertSessionHasErrors(['email' => __('school.errors.already_member')]);

    expect(Invitation::withoutSchoolScope()->count())->toBe(0);
    Mail::assertNothingQueued();
});

it('renews an open invitation instead of adding a second one', function (): void {
    $school = School::factory()->create();
    $operator = operatorOf($school);

    actingAs($operator)->post(route('invitations.store'), ['email' => 'guru@example.com']);
    $first = Invitation::withoutSchoolScope()->sole()->getAttribute('token_hash');
    actingAs($operator)->post(route('invitations.store'), ['email' => 'guru@example.com']);

    expect(Invitation::withoutSchoolScope()->count())->toBe(1)
        ->and(Invitation::withoutSchoolScope()->sole()->getAttribute('token_hash'))->not->toBe($first);
    Mail::assertQueued(InvitationMail::class, 2);
});

it('resends and cancels the school\'s own invitation', function (): void {
    $school = School::factory()->create();
    $operator = operatorOf($school);
    $invitation = Invitation::factory()->for($school)->expired()->create();

    actingAs($operator)->post(route('invitations.resend', $invitation))->assertRedirect(route('teachers.index'));
    expect($invitation->refresh()->expires_at->isFuture())->toBeTrue();
    Mail::assertQueued(InvitationMail::class);

    actingAs($operator)->delete(route('invitations.destroy', $invitation))->assertRedirect(route('teachers.index'));
    expect(Invitation::withoutSchoolScope()->whereKey($invitation->id)->exists())->toBeFalse();
});

it('hides another school\'s invitation (IDOR)', function (): void {
    $operator = operatorOf(School::factory()->create());
    $foreign = Invitation::factory()->for(School::factory()->create())->create();

    actingAs($operator)->post(route('invitations.resend', $foreign))->assertNotFound();
    actingAs($operator)->delete(route('invitations.destroy', $foreign))->assertNotFound();

    expect(Invitation::withoutSchoolScope()->whereKey($foreign->id)->exists())->toBeTrue();
    Mail::assertNothingQueued();
});

it('uses the role the operator switched to, not another membership', function (): void {
    $schoolA = School::factory()->create();
    $schoolB = School::factory()->create();
    $user = User::factory()->operatorAt($schoolA)->create();
    $teacherAtB = SchoolMembership::factory()->for($schoolB)->for($user)->create();
    $invitationA = Invitation::factory()->for($schoolA)->create();

    // As a teacher at school B the user is not an operator, and school A's rows are out of scope.
    actingAs($user)->put(route('role.update'), ['membership' => $teacherAtB->public_id]);
    actingAs($user)->get(route('teachers.index'))->assertForbidden();
    actingAs($user)->delete(route('invitations.destroy', $invitationA))->assertNotFound();
});

it('does not let an operator touch an accepted invitation or a non-teacher invitation', function (): void {
    $school = School::factory()->create();
    $operator = operatorOf($school);
    $accepted = Invitation::factory()->for($school)->accepted()->create();
    $principal = Invitation::factory()->for($school)->role(MembershipRole::Principal)->create();

    actingAs($operator)->delete(route('invitations.destroy', $accepted))->assertForbidden();
    actingAs($operator)->post(route('invitations.resend', $principal))->assertForbidden();
});
