<?php

declare(strict_types=1);

namespace App\Filament\Resources\RegionCalendars\Pages;

use App\Filament\Resources\RegionCalendars\RegionCalendarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListRegionCalendars extends ListRecords
{
    protected static string $resource = RegionCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
