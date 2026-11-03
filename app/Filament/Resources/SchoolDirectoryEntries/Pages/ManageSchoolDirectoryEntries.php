<?php

declare(strict_types=1);

namespace App\Filament\Resources\SchoolDirectoryEntries\Pages;

use App\Filament\Resources\SchoolDirectoryEntries\SchoolDirectoryEntryResource;
use Filament\Resources\Pages\ManageRecords;

final class ManageSchoolDirectoryEntries extends ManageRecords
{
    protected static string $resource = SchoolDirectoryEntryResource::class;
}
