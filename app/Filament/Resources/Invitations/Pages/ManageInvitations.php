<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invitations\Pages;

use App\Filament\Resources\Invitations\InvitationResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

final class ManageInvitations extends ManageRecords
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('console.invitations.actions.create'))
                ->modalSubmitActionLabel(__('console.invitations.actions.send'))
                ->createAnother(false)
                ->successNotificationTitle(__('console.invitations.sent_generic'))
                ->using(fn (array $data, Action $action) => InvitationResource::invite($data, $action)),
        ];
    }
}
