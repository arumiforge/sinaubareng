<?php

declare(strict_types=1);

namespace App\Filament\Resources\SupervisorAssignments\Pages;

use App\Filament\Resources\SupervisorAssignments\SupervisorAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

final class ManageSupervisorAssignments extends ManageRecords
{
    protected static string $resource = SupervisorAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
