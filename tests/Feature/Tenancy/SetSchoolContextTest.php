<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use App\Http\Middleware\SetSchoolContext;
use App\Http\Middleware\UseGuard;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    Route::domain(config('app.hosts.app'))
        ->middleware(['web', UseGuard::class.':web', SetSchoolContext::class])
        ->get('/_context', fn (SchoolContext $context): array => [
            'school_id' => $context->schoolId(),
            'role' => $context->role()?->value,
        ]);
});

function contextUrl(): string
{
    return 'http://'.config('app.hosts.app').'/_context';
}

it('uses the oldest active membership by default', function (): void {
    $school = School::factory()->create();
    $user = User::factory()->teacherAt($school)->create();
    SchoolMembership::factory()->for($user)->role(MembershipRole::Operator)->create();

    actingAs($user)->get(contextUrl())
        ->assertExactJson(['school_id' => $school->id, 'role' => 'teacher'])
        ->assertSessionHas(SetSchoolContext::SESSION_KEY);
});

it('keeps the membership chosen in the session', function (): void {
    $user = User::factory()->teacherAt(School::factory()->create())->create();
    $operator = SchoolMembership::factory()->for($user)->role(MembershipRole::Operator)->create();

    actingAs($user)->withSession([SetSchoolContext::SESSION_KEY => $operator->id])->get(contextUrl())
        ->assertExactJson(['school_id' => $operator->school_id, 'role' => 'operator']);
});

it('ignores ended memberships and memberships of other users', function (): void {
    $user = User::factory()->create();
    SchoolMembership::factory()->for($user)->ended()->create();
    $someoneElse = SchoolMembership::factory()->create();

    actingAs($user)->withSession([SetSchoolContext::SESSION_KEY => $someoneElse->id])->get(contextUrl())
        ->assertExactJson(['school_id' => null, 'role' => null])
        ->assertSessionMissing(SetSchoolContext::SESSION_KEY);
});

it('leaves guests without a school', function (): void {
    $this->get(contextUrl())->assertExactJson(['school_id' => null, 'role' => null]);
});
