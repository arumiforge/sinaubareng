<?php

declare(strict_types=1);

namespace App\Filament\Resources\NationalHolidays\Pages;

use App\Filament\Resources\NationalHolidays\NationalHolidayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

final class ManageNationalHolidays extends ManageRecords
{
    protected static string $resource = NationalHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
