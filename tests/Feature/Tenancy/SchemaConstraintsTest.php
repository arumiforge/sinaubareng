<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use App\Enums\StaffRole;
use App\Models\Invitation;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\StaffUser;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

it('allows only one active principal per school', function (): void {
    $school = School::factory()->create();
    SchoolMembership::factory()->for($school)->role(MembershipRole::Principal)->ended()->create();
    SchoolMembership::factory()->for($school)->role(MembershipRole::Principal)->create();

    expect(fn () => SchoolMembership::factory()->for($school)->role(MembershipRole::Principal)->create())
        ->toThrow(QueryException::class);

    // Another school keeps its own principal.
    SchoolMembership::factory()->role(MembershipRole::Principal)->create();
    expect(SchoolMembership::withoutSchoolScope()->count())->toBe(3);
});

it('keeps one membership per school, user, and role', function (): void {
    $membership = SchoolMembership::factory()->create();

    expect(fn () => SchoolMembership::factory()->create([
        'school_id' => $membership->school_id,
        'user_id' => $membership->user_id,
        'role' => $membership->role,
    ]))->toThrow(QueryException::class);
});

it('rejects values outside the fixed sets', function (string $table, array $row): void {
    expect(fn () => DB::table($table)->insert($row))->toThrow(QueryException::class);
})->with([
    'school level' => fn (): array => ['schools', ['name' => 'X', 'level' => 'smp', 'region_id' => School::factory()->create()->region_id]],
    'membership role' => fn (): array => ['school_memberships', [
        'public_id' => (string) Str::uuid7(), 'school_id' => School::factory()->create()->id, 'user_id' => User::factory()->create()->id,
        'role' => 'student', 'status' => 'active', 'started_on' => '2026-07-01',
    ]],
    'invitation role' => fn (): array => ['invitations', [
        'public_id' => (string) Str::uuid7(), 'school_id' => School::factory()->create()->id, 'email' => 'x@example.com',
        'role' => 'supervisor', 'token_hash' => str_repeat('a', 64), 'expires_at' => '2026-12-01 00:00:00',
    ]],
    'staff role' => fn (): array => ['staff_users', ['name' => 'X', 'email' => 'x@example.com', 'password' => 'x', 'role' => 'owner']],
]);

it('removes memberships with their school but keeps regions that have schools', function (): void {
    $membership = SchoolMembership::factory()->create();
    $region = $membership->school()->withoutGlobalScopes()->firstOrFail()->region;

    expect(fn () => $region->delete())->toThrow(QueryException::class);

    School::query()->whereKey($membership->school_id)->delete();
    expect(SchoolMembership::withoutSchoolScope()->count())->toBe(0);
});

it('casts the staff role', function (): void {
    expect(StaffUser::factory()->create()->role)->toBe(StaffRole::Admin);
});

it('keeps invitation tokens unique and removes invitations with their school', function (): void {
    $invitation = Invitation::factory()->withToken()->create();

    expect(fn () => Invitation::factory()->withToken()->create())->toThrow(QueryException::class);

    School::query()->whereKey($invitation->school_id)->delete();
    expect(Invitation::withoutSchoolScope()->count())->toBe(0);
});

it('gives every membership a public id', function (): void {
    expect(SchoolMembership::factory()->create()->public_id)->toBeString()->toHaveLength(36);
});
