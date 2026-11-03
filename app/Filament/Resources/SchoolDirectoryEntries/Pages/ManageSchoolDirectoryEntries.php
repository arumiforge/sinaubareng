<?php

declare(strict_types=1);

namespace App\Filament\Resources\SchoolDirectoryEntries\Pages;

use App\Exports\SchoolDirectoryExport;
use App\Filament\Pages\SchoolMap;
use App\Filament\Resources\SchoolDirectoryEntries\SchoolDirectoryEntryResource;
use App\Models\School;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ManageSchoolDirectoryEntries extends ManageRecords
{
    protected static string $resource = SchoolDirectoryEntryResource::class;

    public bool $summaryOnly = false;

    public function getSubheading(): ?string
    {
        // Customers without an NPSN cannot be matched to the directory, so they are missing from the counts.
        $withoutNpsn = School::query()->whereNull('npsn')->count();

        return $withoutNpsn > 0 ? trans_choice('console.directory.unmatched', $withoutNpsn, ['count' => $withoutNpsn]) : null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('summary')
                ->label(fn (): string => __($this->summaryOnly ? 'console.directory.actions.list' : 'console.directory.actions.summary'))
                ->icon(fn (): Heroicon => $this->summaryOnly ? Heroicon::OutlinedListBullet : Heroicon::OutlinedChartBar)
                ->color('gray')
                ->action(function (): void {
                    $this->summaryOnly = ! $this->summaryOnly;
                }),
            Action::make('map')
                ->label(__('console.directory.actions.map'))
                ->icon(Heroicon::OutlinedMap)
                ->color('gray')
                ->url(SchoolMap::getUrl()),
            Action::make('download')
                ->label(__('console.directory.actions.download'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->action(fn (): BinaryFileResponse => Excel::download(
                    // The rows as filtered on screen, without pagination (XP-4).
                    new SchoolDirectoryExport($this->getFilteredSortedTableQuery()),
                    __('export.files.school_directory').'-'.now()->format('Y-m-d').'.xlsx',
                )),
        ];
    }
}
