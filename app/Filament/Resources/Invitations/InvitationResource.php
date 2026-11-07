<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invitations;

use App\Enums\InvitationStatus;
use App\Enums\MembershipRole;
use App\Enums\StaffRole;
use App\Filament\Resources\Invitations\Pages\ManageInvitations;
use App\Filament\Resources\StaffAccess;
use App\Models\Invitation;
use App\Models\School;
use App\Models\StaffUser;
use App\Tenancy\Actions\InviteMember;
use App\Tenancy\SchoolScope;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

/**
 * Staff invite a school's first operator or principal (product concept, Operator flow).
 * Invitations are sent and renewed through the same action the operator uses.
 */
final class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static ?string $slug = 'undangan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Support);
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getModelLabel(): string
    {
        return __('console.invitations.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.invitations.plural_label');
    }

    /**
     * @return Builder<Invitation>
     */
    public static function getEloquentQuery(): Builder
    {
        // The staff console sees every school (TEN-2).
        return parent::getEloquentQuery()->withoutGlobalScope(SchoolScope::class);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label(__('console.invitations.fields.school'))
                    ->options(fn (): array => School::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                TextInput::make('email')
                    ->label(__('console.invitations.fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('role')
                    ->label(__('console.invitations.fields.role'))
                    ->options(StaffAccess::options(MembershipRole::class))
                    ->default(MembershipRole::Operator->value)
                    ->required(),
            ]);
    }

    /**
     * Sends through InviteMember, so the console and the operator follow one set of rules.
     *
     * @param  array{school_id: int|string, email: string, role: string}  $data
     */
    public static function invite(array $data, Action $action): Invitation
    {
        return self::sending($action, fn (StaffUser $staff): Invitation => app(InviteMember::class)->handle(
            School::query()->findOrFail($data['school_id']),
            $data['email'],
            MembershipRole::from($data['role']),
            $staff,
        ));
    }

    /**
     * @param  callable(StaffUser): Invitation  $send
     */
    private static function sending(Action $action, callable $send): Invitation
    {
        $staff = auth('staff')->user();
        abort_unless($staff instanceof StaffUser, 403);

        try {
            return $send($staff);
        } catch (ValidationException $exception) {
            Notification::make()->danger()->title($exception->validator->errors()->first())->send();
            $action->halt();

            throw $exception; // halt() always throws; this keeps the return type honest.
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['school:id,name', 'inviter:id,name', 'staffInviter:id,name']))
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('school.name')
                    ->label(__('console.invitations.fields.school'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('console.invitations.fields.email'))
                    ->searchable(),
                TextColumn::make('role')
                    ->label(__('console.invitations.fields.role'))
                    ->formatStateUsing(fn (MembershipRole $state): string => $state->label())
                    ->badge(),
                TextColumn::make('invitation_status')
                    ->label(__('console.invitations.fields.status'))
                    ->state(fn (Invitation $record): string => $record->status()->label())
                    ->badge()
                    ->color(fn (Invitation $record): string => match ($record->status()) {
                        InvitationStatus::Accepted => 'success',
                        InvitationStatus::Expired => 'warning',
                        InvitationStatus::Pending => 'gray',
                    }),
                TextColumn::make('expires_at')
                    ->label(__('console.invitations.fields.expires_at'))
                    ->dateTime('d/m/Y H.i'),
                TextColumn::make('invited_by_name')
                    ->label(__('console.invitations.fields.invited_by'))
                    ->state(fn (Invitation $record): ?string => $record->staffInviter?->name ?? $record->inviter?->name),
            ])
            ->filters([
                SelectFilter::make('school')
                    ->label(__('console.invitations.fields.school'))
                    ->relationship('school', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('resend')
                    ->label(__('console.invitations.actions.resend'))
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->visible(fn (Invitation $record): bool => $record->accepted_at === null)
                    ->requiresConfirmation()
                    ->action(function (Invitation $record, Action $action): void {
                        self::sending($action, fn (StaffUser $staff): Invitation => app(InviteMember::class)->resend($record, $staff));
                        Notification::make()->success()->title(__('console.invitations.sent', ['email' => $record->email]))->send();
                    }),
                DeleteAction::make()
                    ->label(__('console.invitations.actions.cancel'))
                    ->visible(fn (Invitation $record): bool => $record->accepted_at === null)
                    ->modalDescription(fn (Invitation $record): string => __('console.invitations.cancel_warning', ['email' => $record->email])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInvitations::route('/'),
        ];
    }
}
