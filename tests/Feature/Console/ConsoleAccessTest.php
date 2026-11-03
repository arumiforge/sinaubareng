<?php

declare(strict_types=1);

use App\Enums\SchoolLevel;
use App\Enums\StaffRole;
use App\Filament\Resources\Regions\Pages\ManageRegions;
use App\Filament\Resources\Schools\Pages\ManageSchools;
use App\Filament\Resources\StaffUsers\Pages\ManageStaffUsers;
use App\Models\Region;
use App\Models\School;
use App\Models\StaffUser;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

function adminUrl(string $path): string
{
    return 'http://'.config('app.hosts.admin').$path;
}

function staff(StaffRole $role): StaffUser
{
    return StaffUser::factory()->role($role)->create();
}

beforeEach(function (): void {
    Filament::setCurrentPanel('admin');
});

it('opens each console page for the staff roles that may use it', function (StaffRole $role, string $path, int $status): void {
    actingAs(staff($role), 'staff')->get(adminUrl($path))->assertStatus($status);
})->with([
    'admin, schools' => [StaffRole::Admin, '/sekolah', 200],
    'admin, regions' => [StaffRole::Admin, '/wilayah', 200],
    'admin, staff' => [StaffRole::Admin, '/staf', 200],
    'support, schools' => [StaffRole::Support, '/sekolah', 200],
    'support, staff' => [StaffRole::Support, '/staf', 403],
    'content, schools' => [StaffRole::Content, '/sekolah', 403],
    'content, regions' => [StaffRole::Content, '/wilayah', 403],
]);

it('keeps school users out of the console', function (): void {
    $this->get(adminUrl('/sekolah'))->assertRedirect(adminUrl('/masuk'));
});

it('lets staff create a school', function (): void {
    $region = Region::factory()->create();
    actingAs(staff(StaffRole::Support), 'staff');

    Livewire::test(ManageSchools::class)
        ->callAction(CreateAction::class, data: [
            'name' => 'SD Negeri 1 Kudus',
            'npsn' => '20317001',
            'level' => SchoolLevel::Sd->value,
            'region_id' => $region->id,
        ])
        ->assertHasNoFormErrors();

    expect(School::query()->where('npsn', '20317001')->sole())
        ->name->toBe('SD Negeri 1 Kudus')
        ->level->toBe(SchoolLevel::Sd);
});

it('validates the NPSN', function (): void {
    actingAs(staff(StaffRole::Admin), 'staff');

    Livewire::test(ManageSchools::class)
        ->callAction(CreateAction::class, data: [
            'name' => 'SD X',
            'npsn' => '123',
            'level' => SchoolLevel::Sd->value,
            'region_id' => Region::factory()->create()->id,
        ])
        ->assertHasFormErrors(['npsn']);
});

it('does not offer to delete a region that has schools', function (): void {
    $school = School::factory()->create();
    $empty = Region::factory()->create();
    actingAs(staff(StaffRole::Admin), 'staff');

    Livewire::test(ManageRegions::class)
        ->assertActionHidden(TestAction::make('delete')->table($school->region))
        ->assertActionVisible(TestAction::make('delete')->table($empty));
});

it('hashes a new staff password and never offers to delete your own account', function (): void {
    $admin = staff(StaffRole::Admin);
    actingAs($admin, 'staff');

    Livewire::test(ManageStaffUsers::class)
        ->callAction(CreateAction::class, data: [
            'name' => 'Tim Dukungan',
            'email' => 'dukungan@example.com',
            'password' => 'kata-sandi-panjang',
            'role' => StaffRole::Support->value,
        ])
        ->assertHasNoFormErrors();

    $created = StaffUser::query()->where('email', 'dukungan@example.com')->sole();
    expect(Hash::check('kata-sandi-panjang', $created->password))->toBeTrue();

    Livewire::test(ManageStaffUsers::class)
        ->assertActionHidden(TestAction::make('delete')->table($admin))
        ->assertActionVisible(TestAction::make('delete')->table($created));
});

it('keeps a staff password when the field is left empty on edit', function (): void {
    actingAs(staff(StaffRole::Admin), 'staff');
    $other = staff(StaffRole::Support);
    $hash = $other->password;

    Livewire::test(ManageStaffUsers::class)
        ->callAction(TestAction::make('edit')->table($other), data: ['name' => 'Nama Baru', 'password' => ''])
        ->assertHasNoFormErrors();

    expect($other->refresh())->name->toBe('Nama Baru')->password->toBe($hash);
});
