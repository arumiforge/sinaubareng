<?php

declare(strict_types=1);

use App\Enums\SchoolLevel;
use App\Enums\StaffRole;
use App\Filament\Resources\Schools\Pages\ManageSchools;
use App\Models\Region;
use App\Models\SchoolDirectoryEntry;
use App\Models\StaffUser;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

function directoryFixture(): string
{
    return base_path('tests/Fixtures/school-directory.json');
}

it('imports the directory and its region, and can run again', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();

    $region = Region::query()->sole();
    expect($region->only(['province', 'regency']))->toBe(['province' => 'Jawa Tengah', 'regency' => 'Kabupaten Kudus'])
        ->and(SchoolDirectoryEntry::query()->count())->toBe(2)
        ->and(SchoolDirectoryEntry::query()->firstWhere('npsn', '60712382'))
        ->level->toBe(SchoolLevel::Mi)
        ->region_id->toBe($region->id);
});

it('updates changed schools and drops schools missing from a newer file', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();

    $data = json_decode((string) file_get_contents(directoryFixture()), true);
    $data['schools'] = [[...$data['schools'][0], 'name' => 'SD NEGERI 3 MEGAWON']];
    $newer = tempnam(sys_get_temp_dir(), 'dir').'.json';
    file_put_contents($newer, json_encode($data));

    $this->artisan('school-directory:import', ['file' => $newer])->assertSuccessful();

    expect(SchoolDirectoryEntry::query()->pluck('name', 'npsn')->all())->toBe(['20317288' => 'SD NEGERI 3 MEGAWON']);
});

it('refuses a malformed file and changes nothing', function (): void {
    $bad = tempnam(sys_get_temp_dir(), 'dir').'.json';
    file_put_contents($bad, json_encode([
        'fetched_on' => '2026-11-03', 'province' => 'Jawa Tengah', 'regency' => 'Kabupaten Kudus',
        'schools' => [['npsn' => '123', 'name' => 'X', 'level' => 'smp', 'address' => '', 'village' => '', 'district' => 'Jati']],
    ]));

    $this->artisan('school-directory:import', ['file' => $bad])->assertFailed();
    $this->artisan('school-directory:import', ['file' => '/nonexistent.json'])->assertFailed();

    expect(SchoolDirectoryEntry::query()->count())->toBe(0)->and(Region::query()->count())->toBe(0);
});

it('fills a new school from the directory when staff type its NPSN', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    Filament::setCurrentPanel('admin');
    actingAs(StaffUser::factory()->role(StaffRole::Support)->create(), 'staff');

    Livewire::test(ManageSchools::class)
        ->mountAction(CreateAction::class)
        ->fillForm(['npsn' => '60712382'])
        ->assertSchemaStateSet([
            'name' => 'MIN KUDUS',
            'level' => 'mi',
            'region_id' => Region::query()->value('id'),
            'address' => 'JALAN KADILANGU 549, Prambatan Kidul, Kaliwungu',
        ]);
});

it('keeps what staff already typed', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    Filament::setCurrentPanel('admin');
    actingAs(StaffUser::factory()->create(), 'staff');

    Livewire::test(ManageSchools::class)
        ->mountAction(CreateAction::class)
        ->fillForm(['name' => 'Nama dari staf', 'npsn' => '60712382'])
        ->assertSchemaStateSet(['name' => 'Nama dari staf', 'level' => 'mi']);
});
