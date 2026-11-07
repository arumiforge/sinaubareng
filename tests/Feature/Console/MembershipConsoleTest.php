<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Enums\StaffRole;
use App\Filament\Resources\Invitations\Pages\ManageInvitations;
use App\Filament\Resources\SchoolMemberships\Pages\ManageSchoolMemberships;
use App\Filament\Resources\SupervisorAssignments\Pages\ManageSupervisorAssignments;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\StaffUser;
use App\Models\SupervisorAssignment;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    Filament::setCurrentPanel('admin');
    Mail::fake();
});

function consoleUrl(string $path): string
{
    return 'http://'.config('app.hosts.admin').$path;
}

it('opens the membership, invitation, and supervisor pages for admin and support only', function (StaffRole $role, int $status): void {
    $staff = StaffUser::factory()->role($role)->create();

    foreach (['/anggota-sekolah', '/undangan', '/penugasan-pengawas'] as $path) {
        actingAs($staff, 'staff')->get(consoleUrl($path))->assertStatus($status);
    }
})->with([
    'admin' => [StaffRole::Admin, 200],
    'support' => [StaffRole::Support, 200],
    'content' => [StaffRole::Content, 403],
]);

it('keeps school users out of the new console pages', function (): void {
    actingAs(User::factory()->operatorAt(School::factory()->create())->create())
        ->get(consoleUrl('/undangan'))
        ->assertRedirect(consoleUrl('/masuk'));
});

it('lets staff invite a school\'s first operator', function (): void {
    $staff = StaffUser::factory()->role(StaffRole::Support)->create();
    $school = School::factory()->create();
    actingAs($staff, 'staff');

    Livewire::test(ManageInvitations::class)
        ->callAction(CreateAction::class, data: [
            'school_id' => $school->id,
            'email' => 'operator@example.com',
            'role' => MembershipRole::Operator->value,
        ])
        ->assertHasNoFormErrors();

    expect(Invitation::withoutSchoolScope()->sole())
        ->school_id->toBe($school->id)
        ->role->toBe(MembershipRole::Operator)
        ->invited_by->toBeNull()
        ->invited_by_staff_id->toBe($staff->id);
    Mail::assertQueued(InvitationMail::class, fn (InvitationMail $mail): bool => $mail->hasTo('operator@example.com'));
});

it('refuses a second principal invitation from the console', function (): void {
    $school = School::factory()->create();
    User::factory()->principalAt($school)->create();
    actingAs(StaffUser::factory()->role(StaffRole::Admin)->create(), 'staff');

    Livewire::test(ManageInvitations::class)
        ->callAction(CreateAction::class, data: [
            'school_id' => $school->id,
            'email' => 'kepala@example.com',
            'role' => MembershipRole::Principal->value,
        ])
        ->assertNotified(__('school.errors.principal_taken'));

    expect(Invitation::withoutSchoolScope()->count())->toBe(0);
    Mail::assertNothingQueued();
});

it('resends an open invitation and hides resend on accepted ones', function (): void {
    actingAs(StaffUser::factory()->role(StaffRole::Admin)->create(), 'staff');
    $open = Invitation::factory()->expired()->create();
    $accepted = Invitation::factory()->accepted()->create();

    Livewire::test(ManageInvitations::class)
        ->assertActionHidden(TestAction::make('resend')->table($accepted))
        ->callAction(TestAction::make('resend')->table($open));

    expect($open->refresh()->expires_at->isFuture())->toBeTrue();
    Mail::assertQueued(InvitationMail::class, 1);
});

it('ends a membership and keeps one active principal per school', function (): void {
    $school = School::factory()->create();
    $old = SchoolMembership::factory()->for($school)->role(MembershipRole::Principal)->create();
    $new = SchoolMembership::factory()->for($school)->role(MembershipRole::Principal)->ended()->create();
    actingAs(StaffUser::factory()->role(StaffRole::Admin)->create(), 'staff');

    Livewire::test(ManageSchoolMemberships::class)
        ->callAction(TestAction::make('edit')->table($new), data: ['status' => MembershipStatus::Active->value, 'ended_on' => null])
        ->assertHasFormErrors(['status']);

    Livewire::test(ManageSchoolMemberships::class)
        ->callAction(TestAction::make('edit')->table($old), data: ['status' => MembershipStatus::Ended->value, 'ended_on' => now()->toDateString()])
        ->assertHasNoFormErrors();

    Livewire::test(ManageSchoolMemberships::class)
        ->callAction(TestAction::make('edit')->table($new), data: ['status' => MembershipStatus::Active->value, 'ended_on' => null])
        ->assertHasNoFormErrors();

    expect($old->refresh()->status)->toBe(MembershipStatus::Ended)
        ->and($new->refresh()->status)->toBe(MembershipStatus::Active);
});

it('gives an existing account a role at another school', function (): void {
    $user = User::factory()->teacherAt(School::factory()->create())->create();
    $school = School::factory()->create();
    actingAs(StaffUser::factory()->role(StaffRole::Support)->create(), 'staff');

    Livewire::test(ManageSchoolMemberships::class)
        ->callAction(CreateAction::class, data: [
            'school_id' => $school->id,
            'user_id' => $user->id,
            'role' => MembershipRole::Operator->value,
            'status' => MembershipStatus::Active->value,
            'started_on' => now()->toDateString(),
        ])
        ->assertHasNoFormErrors();

    expect(SchoolMembership::withoutSchoolScope()->whereBelongsTo($user)->where('school_id', $school->id)->sole()->role)
        ->toBe(MembershipRole::Operator);
});

it('assigns a supervisor to a school', function (): void {
    $supervisor = User::factory()->create();
    $school = School::factory()->create();
    actingAs(StaffUser::factory()->role(StaffRole::Admin)->create(), 'staff');

    Livewire::test(ManageSupervisorAssignments::class)
        ->callAction(CreateAction::class, data: [
            'user_id' => $supervisor->id,
            'school_id' => $school->id,
            'started_on' => '2026-07-13',
        ])
        ->assertHasNoFormErrors();

    expect(SupervisorAssignment::query()->sole())
        ->user_id->toBe($supervisor->id)
        ->school_id->toBe($school->id);

    Livewire::test(ManageSupervisorAssignments::class)
        ->callAction(CreateAction::class, data: [
            'user_id' => $supervisor->id,
            'school_id' => $school->id,
            'started_on' => '2026-07-13',
        ])
        ->assertHasFormErrors(['started_on']);
});
