<?php

declare(strict_types=1);

namespace App\Filament\Resources\StaffUsers;

use App\Enums\StaffRole;
use App\Filament\Resources\StaffAccess;
use App\Filament\Resources\StaffUsers\Pages\ManageStaffUsers;
use App\Models\StaffUser;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;

final class StaffUserResource extends Resource
{
    protected static ?string $model = StaffUser::class;

    protected static ?string $slug = 'staf';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin);
    }

    public static function getModelLabel(): string
    {
        return __('console.staff_users.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.staff_users.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('console.staff_users.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('console.staff_users.fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label(__('console.staff_users.fields.password'))
                    ->helperText(__('console.staff_users.hints.password'))
                    ->password()
                    ->revealable()
                    ->rule(Password::min(12))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    // Keep the current password when the field is left empty on edit.
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('role')
                    ->label(__('console.staff_users.fields.role'))
                    ->options(StaffAccess::options(StaffRole::class))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('console.staff_users.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('console.staff_users.fields.email'))
                    ->searchable(),
                TextColumn::make('role')
                    ->label(__('console.staff_users.fields.role'))
                    ->formatStateUsing(fn (StaffRole $state): string => $state->label())
                    ->badge(),
            ])
            ->recordActions([
                EditAction::make(),
                // Staff cannot delete their own account, so the console always keeps an admin.
                DeleteAction::make()->hidden(fn (StaffUser $record): bool => $record->is(auth('staff')->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStaffUsers::route('/'),
        ];
    }
}
