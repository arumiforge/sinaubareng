<?php

declare(strict_types=1);

namespace App\Filament\Resources\RegionCalendars\Pages;

use App\Filament\Resources\RegionCalendars\RegionCalendarResource;
use Filament\Resources\Pages\EditRecord;

final class EditRegionCalendar extends EditRecord
{
    protected static string $resource = RegionCalendarResource::class;
}
