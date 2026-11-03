<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\StaffRole;
use App\Filament\Resources\StaffAccess;
use App\Models\Region;
use App\Models\SchoolDirectoryEntry;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

/**
 * Directory schools on a map: customers and schools to approach next (D-170).
 */
final class SchoolMap extends Page
{
    protected string $view = 'filament.pages.school-map';

    protected static ?string $slug = 'peta-sekolah';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Support);
    }

    public static function getNavigationLabel(): string
    {
        return __('console.map.title');
    }

    public function getTitle(): string
    {
        return __('console.map.title');
    }

    /**
     * Everything the map script needs, as one JSON object.
     *
     * @return array<string, mixed>
     */
    public function mapData(): array
    {
        $points = SchoolDirectoryEntry::query()
            ->whereNotNull('latitude')
            ->with('region')
            ->withExists('school')
            ->orderBy('npsn')
            ->get()
            ->map(fn (SchoolDirectoryEntry $entry): array => [
                'npsn' => $entry->npsn,
                'name' => $entry->name,
                'level' => $entry->level->label(),
                'district' => $entry->district,
                'regency' => $entry->region->regency,
                'customer' => (bool) $entry->getAttribute('school_exists'),
                'lat' => $entry->latitude,
                'lng' => $entry->longitude,
            ]);

        return [
            'points' => $points->all(),
            'labels' => [
                'customer' => __('console.directory.filters.customers'),
                'prospect' => __('console.directory.filters.not_customers'),
                'count' => __('console.map.count'),
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public function regencies(): array
    {
        return Region::query()->whereHas('directoryEntries')->orderBy('regency')->pluck('regency')->all();
    }

    public function missingLocations(): int
    {
        return SchoolDirectoryEntry::query()->whereNull('latitude')->count();
    }
}
