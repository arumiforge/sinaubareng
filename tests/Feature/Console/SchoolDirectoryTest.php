<?php

declare(strict_types=1);

use App\Enums\SchoolLevel;
use App\Enums\StaffRole;
use App\Exports\SchoolDirectoryExport;
use App\Filament\Pages\SchoolMap;
use App\Filament\Resources\SchoolDirectoryEntries\Pages\ManageSchoolDirectoryEntries;
use App\Filament\Resources\Schools\Pages\ManageSchools;
use App\Models\Region;
use App\Models\School;
use App\Models\SchoolDirectoryEntry;
use App\Models\StaffUser;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

it('shows staff which directory schools are already customers', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    $customer = SchoolDirectoryEntry::query()->firstWhere('npsn', '60712382');
    School::factory()->create(['npsn' => $customer->npsn, 'region_id' => $customer->region_id]);
    $prospect = SchoolDirectoryEntry::query()->firstWhere('npsn', '20317288');

    Filament::setCurrentPanel('admin');
    actingAs(StaffUser::factory()->role(StaffRole::Support)->create(), 'staff');

    Livewire::test(ManageSchoolDirectoryEntries::class)
        ->assertCanSeeTableRecords([$customer, $prospect])
        ->filterTable('is_customer', false)
        ->assertCanSeeTableRecords([$prospect])
        ->assertCanNotSeeTableRecords([$customer])
        ->filterTable('is_customer', true)
        ->assertCanSeeTableRecords([$customer])
        ->assertCanNotSeeTableRecords([$prospect]);
});

it('keeps the directory page to admin and support staff', function (StaffRole $role, int $status): void {
    actingAs(StaffUser::factory()->role($role)->create(), 'staff')
        ->get('http://'.config('app.hosts.admin').'/sebaran-sekolah')
        ->assertStatus($status);
})->with([
    'admin' => [StaffRole::Admin, 200],
    'support' => [StaffRole::Support, 200],
    'content' => [StaffRole::Content, 403],
]);

it('marks which schools match the directory', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    $matched = School::factory()->create(['npsn' => '60712382']);
    $missing = School::factory()->create(['npsn' => null]);
    $elsewhere = School::factory()->create(['npsn' => '99999999']);

    Filament::setCurrentPanel('admin');
    actingAs(StaffUser::factory()->create(), 'staff');

    Livewire::test(ManageSchools::class)
        ->assertTableColumnStateSet('directory_match', 'matched', $matched)
        ->assertTableColumnStateSet('directory_match', 'missing_npsn', $missing)
        ->assertTableColumnStateSet('directory_match', 'not_in_directory', $elsewhere)
        ->filterTable('directory_match', 'missing_npsn')
        ->assertCanSeeTableRecords([$missing])
        ->assertCanNotSeeTableRecords([$matched, $elsewhere]);

    Livewire::test(ManageSchoolDirectoryEntries::class)
        ->assertSee('1 sekolah pelanggan belum punya NPSN');
});

it('downloads the filtered directory as Excel with Indonesian headings', function (): void {
    Excel::fake();
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    School::factory()->create(['npsn' => '60712382']);

    Filament::setCurrentPanel('admin');
    actingAs(StaffUser::factory()->create(), 'staff');

    Livewire::test(ManageSchoolDirectoryEntries::class)
        ->filterTable('is_customer', false)
        ->callAction('download');

    Excel::assertDownloaded(
        'sebaran-sekolah-'.now()->format('Y-m-d').'.xlsx',
        function (SchoolDirectoryExport $export): bool {
            $rows = $export->query()->get()->map(fn ($row) => $export->map($row));

            return $export->headings()[0] === 'NPSN'
                && $export->headings()[7] === 'Sudah Pelanggan'
                && $rows->pluck(0)->all() === ['20317288']
                && $rows->first()[7] === 'Tidak';
        },
    );
});

it('writes NPSN and names as text cells', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    $export = new SchoolDirectoryExport(SchoolDirectoryEntry::query()->with('region')->withExists('school'));
    $path = 'tmp/directory-test.xlsx';

    Excel::store($export, $path, 'local');
    $sheet = IOFactory::load(Storage::disk('local')->path($path))->getActiveSheet();

    expect($sheet->getTitle())->toBe('Sebaran sekolah')
        ->and($sheet->getCell('A2')->getDataType())->toBe('s')
        ->and($sheet->getCell('A2')->getValue())->toBe('20317288')
        ->and($sheet->getFreezePane())->toBe('A2')
        ->and($sheet->getAutoFilter()->getRange())->not->toBe('');

    Storage::disk('local')->delete($path);
});

it('puts directory schools with a location on the map', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    SchoolDirectoryEntry::query()->where('npsn', '60712382')->update(['latitude' => -6.8033, 'longitude' => 110.8233]);
    School::factory()->create(['npsn' => '60712382']);

    $page = new SchoolMap;
    $data = $page->mapData();

    expect($data['points'])->toHaveCount(1)
        ->and($data['points'][0])->toMatchArray(['npsn' => '60712382', 'customer' => true, 'lat' => -6.8033, 'lng' => 110.8233])
        ->and($page->missingLocations())->toBe(1)
        ->and($page->regencies())->toBe(['Kabupaten Kudus']);
});

it('opens the map for admin and support only', function (StaffRole $role, int $status): void {
    actingAs(StaffUser::factory()->role($role)->create(), 'staff')
        ->get('http://'.config('app.hosts.admin').'/peta-sekolah')
        ->assertStatus($status);
})->with([
    'admin' => [StaffRole::Admin, 200],
    'support' => [StaffRole::Support, 200],
    'content' => [StaffRole::Content, 403],
]);

it('imports coordinates and refuses ones outside Indonesia', function (): void {
    $data = json_decode((string) file_get_contents(directoryFixture()), true);
    $data['schools'][0] += ['latitude' => -6.8175, 'longitude' => 110.8783];
    $data['schools'][1] += ['latitude' => null, 'longitude' => null];
    $file = tempnam(sys_get_temp_dir(), 'dir').'.json';
    file_put_contents($file, json_encode($data));

    $this->artisan('school-directory:import', ['file' => $file])->assertSuccessful();
    expect(SchoolDirectoryEntry::query()->firstWhere('npsn', '20317288'))
        ->latitude->toBe(-6.8175)->longitude->toBe(110.8783);

    $data['schools'][0]['latitude'] = 40.7;
    file_put_contents($file, json_encode($data));
    $this->artisan('school-directory:import', ['file' => $file])->assertFailed();
});

it('switches between the school list and one summary row per group', function (): void {
    $this->artisan('school-directory:import', ['file' => directoryFixture()])->assertSuccessful();
    Filament::setCurrentPanel('admin');
    actingAs(StaffUser::factory()->create(), 'staff');

    Livewire::test(ManageSchoolDirectoryEntries::class)
        ->assertSee('SD 3 MEGAWON')
        ->callAction('summary')
        ->assertSet('summaryOnly', true)
        ->assertDontSee('SD 3 MEGAWON')
        ->assertSee('Jati')
        ->callAction('summary')
        ->assertSee('SD 3 MEGAWON');
});
