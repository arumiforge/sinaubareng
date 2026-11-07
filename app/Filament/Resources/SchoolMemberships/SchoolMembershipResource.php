<?php

declare(strict_types=1);

namespace App\Filament\Resources\SchoolMemberships;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Enums\StaffRole;
use App\Filament\Resources\SchoolMemberships\Pages\ManageSchoolMemberships;
use App\Filament\Resources\StaffAccess;
use App\Models\SchoolMembership;
use App\Models\User;
use App\Tenancy\SchoolScope;
use BackedEnum;
use Closure;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

/**
 * Who holds which role at which school (D-167). New people join through an invitation;
 * here staff fix roles, end or restore a membership, or give an existing account a role.
 */
final class SchoolMembershipResource extends Resource
{
    protected static ?string $model = SchoolMembership::class;

    protected static ?string $slug = 'anggota-sekolah';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Support);
    }

    public static function getModelLabel(): string
    {
        return __('console.memberships.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.memberships.plural_label');
    }

    /**
     * @return Builder<SchoolMembership>
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
                    ->label(__('console.memberships.fields.school'))
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit'),
                Select::make('user_id')
                    ->label(__('console.memberships.fields.user'))
                    ->relationship('user', 'email')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} ({$record->email})")
                    ->searchable(['name', 'email'])
                    ->required()
                    ->disabledOn('edit'),
                Select::make('role')
                    ->label(__('console.memberships.fields.role'))
                    ->options(StaffAccess::options(MembershipRole::class))
                    ->required()
                    ->disabledOn('edit')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule
                            ->where('school_id', $get('school_id'))
                            ->where('user_id', $get('user_id')),
                    ),
                Select::make('status')
                    ->label(__('console.memberships.fields.status'))
                    ->options(StaffAccess::options(MembershipStatus::class))
                    ->default(MembershipStatus::Active->value)
                    ->required()
                    ->live()
                    ->rule(fn (Get $get, ?SchoolMembership $record): Closure => self::onePrincipalRule($get, $record)),
                DatePicker::make('started_on')
                    ->label(__('console.memberships.fields.started_on'))
                    ->default(now())
                    ->required(),
                DatePicker::make('ended_on')
                    ->label(__('console.memberships.fields.ended_on'))
                    ->afterOrEqual('started_on')
                    ->required(fn (Get $get): bool => $get('status') === MembershipStatus::Ended->value),
            ]);
    }

    /**
     * One active principal per school; the DB index enforces it too (data model).
     */
    private static function onePrincipalRule(Get $get, ?SchoolMembership $record): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
            $role = $get('role') ?? $record?->role->value;

            if ($value !== MembershipStatus::Active->value || $role !== MembershipRole::Principal->value) {
                return;
            }

            // Checks across the chosen school, so it skips the school scope (TEN-2).
            $taken = SchoolMembership::withoutSchoolScope()
                ->where('school_id', $get('school_id') ?? $record?->school_id)
                ->where('role', MembershipRole::Principal)
                ->active()
                ->when($record, fn (Builder $query) => $query->whereKeyNot($record?->getKey()))
                ->exists();

            if ($taken) {
                $fail(__('console.memberships.errors.principal_taken'));
            }
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['school:id,name', 'user:id,name,email']))
            ->defaultSort('started_on', 'desc')
            ->columns([
                TextColumn::make('school.name')
                    ->label(__('console.memberships.fields.school'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('console.memberships.fields.name'))
                    ->description(fn (SchoolMembership $record): string => $record->user->email)
                    ->searchable(['name', 'email']),
                TextColumn::make('role')
                    ->label(__('console.memberships.fields.role'))
                    ->formatStateUsing(fn (MembershipRole $state): string => $state->label())
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('console.memberships.fields.status'))
                    ->formatStateUsing(fn (MembershipStatus $state): string => $state->label())
                    ->badge()
                    ->color(fn (MembershipStatus $state): string => $state === MembershipStatus::Active ? 'success' : 'gray'),
                TextColumn::make('started_on')
                    ->label(__('console.memberships.fields.started_on'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('ended_on')
                    ->label(__('console.memberships.fields.ended_on'))
                    ->date('d/m/Y')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('school')
                    ->label(__('console.memberships.fields.school'))
                    ->relationship('school', 'name')
                    ->searchable(),
                SelectFilter::make('role')
                    ->label(__('console.memberships.fields.role'))
                    ->options(StaffAccess::options(MembershipRole::class)),
                SelectFilter::make('status')
                    ->label(__('console.memberships.fields.status'))
                    ->options(StaffAccess::options(MembershipStatus::class)),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription(fn (SchoolMembership $record): string => __('console.memberships.delete_warning', [
                        'name' => $record->user->name,
                        'role' => $record->role->label(),
                        'school' => $record->school->name,
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchoolMemberships::route('/'),
        ];
    }
}
