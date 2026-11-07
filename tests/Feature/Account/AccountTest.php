<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use App\Http\Middleware\SetSchoolContext;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

it('shows the own profile and the roles to switch between', function (): void {
    $school = School::factory()->create(['name' => 'MI Nurul Huda']);
    $user = User::factory()->teacherAt($school)->create(['nip' => '198501012010011001']);

    actingAs($user)->get(route('account.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Account/Edit')
            ->where('profile', ['name' => $user->name, 'email' => $user->email, 'nip' => '198501012010011001'])
            ->has('auth.memberships', 1)
            ->where('auth.memberships.0.school', 'MI Nurul Huda')
            ->where('auth.memberships.0.role', 'teacher')
            ->where('auth.memberships.0.current', true)
            ->where('translations', fn ($translations): bool => isset($translations['account.titles.edit'])));
});

it('updates the profile', function (): void {
    $user = User::factory()->create();

    actingAs($user)->put(route('account.update'), ['name' => 'Budi Santoso, S.Pd.', 'nip' => '198501012010011001'])
        ->assertRedirect(route('account.edit'))
        ->assertSessionHas('toast', __('account.toasts.profile_saved'));

    expect($user->refresh())->name->toBe('Budi Santoso, S.Pd.')->nip->toBe('198501012010011001');
});

it('validates the profile and never changes the email', function (): void {
    $user = User::factory()->create();
    $email = $user->email;

    actingAs($user)->put(route('account.update'), ['name' => '', 'nip' => '123'])
        ->assertSessionHasErrors(['name', 'nip']);

    actingAs($user)->put(route('account.update'), ['name' => 'Nama', 'email' => 'lain@example.com']);

    expect($user->refresh()->email)->toBe($email);
});

it('changes the password after checking the current one', function (): void {
    $user = User::factory()->create();

    actingAs($user)->put(route('account.password.update'), [
        'current_password' => 'salah',
        'password' => 'sandiBaru123',
        'password_confirmation' => 'sandiBaru123',
    ])->assertSessionHasErrors('current_password');

    actingAs($user)->put(route('account.password.update'), [
        'current_password' => 'password',
        'password' => 'sandiBaru123',
        'password_confirmation' => 'sandiBaru123',
    ])->assertRedirect(route('account.edit'))->assertSessionHasNoErrors();

    expect(Hash::check('sandiBaru123', $user->refresh()->password))->toBeTrue();
});

it('switches to another of the user\'s roles', function (): void {
    $user = User::factory()->teacherAt(School::factory()->create())->create();
    $operator = SchoolMembership::factory()->for($user)->role(MembershipRole::Operator)->create();

    actingAs($user)->put(route('role.update'), ['membership' => $operator->public_id])
        ->assertRedirect(route('home'))
        ->assertSessionHas(SetSchoolContext::SESSION_KEY, $operator->id);

    actingAs($user)->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.memberships.1.current', true));
});

it('refuses to switch to someone else\'s or an ended membership', function (string $case): void {
    $user = User::factory()->teacherAt(School::factory()->create())->create();
    $target = match ($case) {
        'other user' => SchoolMembership::factory()->role(MembershipRole::Operator)->create(),
        'ended' => SchoolMembership::factory()->for($user)->role(MembershipRole::Operator)->ended()->create(),
    };

    actingAs($user)->put(route('role.update'), ['membership' => $target->public_id])
        ->assertSessionHasErrors(['membership' => __('account.errors.role_unavailable')]);

    expect(session(SetSchoolContext::SESSION_KEY))->not->toBe($target->id);
})->with(['other user', 'ended']);

it('only offers the user\'s own active roles', function (): void {
    $user = User::factory()->teacherAt(School::factory()->create())->create();
    SchoolMembership::factory()->for($user)->role(MembershipRole::Operator)->ended()->create();
    SchoolMembership::factory()->role(MembershipRole::Principal)->create();

    actingAs($user)->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->has('auth.memberships', 1));
});
