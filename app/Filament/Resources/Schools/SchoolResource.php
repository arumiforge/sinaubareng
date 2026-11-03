<?php

declare(strict_types=1);

namespace App\Filament\Resources\Schools;

use App\Enums\SchoolLevel;
use App\Enums\StaffRole;
use App\Filament\Resources\Schools\Pages\ManageSchools;
use App\Filament\Resources\StaffAccess;
use App\Models\Region;
use App\Models\School;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $slug = 'sekolah';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Support);
    }

    public static function getModelLabel(): string
    {
        return __('console.schools.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.schools.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('console.schools.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('npsn')
                    ->label(__('console.schools.fields.npsn'))
                    ->helperText(__('console.schools.hints.npsn'))
                    ->regex('/^\d{8}$/')
                    ->unique(ignoreRecord: true),
                Select::make('level')
                    ->label(__('console.schools.fields.level'))
                    ->options(StaffAccess::options(SchoolLevel::class))
                    ->required(),
                Select::make('region_id')
                    ->label(__('console.schools.fields.region'))
                    ->relationship('region', 'regency', fn (Builder $query): Builder => $query->orderBy('province')->orderBy('regency'))
                    ->getOptionLabelFromRecordUsing(fn (Region $record): string => "{$record->regency}, {$record->province}")
                    ->searchable(['province', 'regency'])
                    ->preload()
                    ->required(),
                Textarea::make('address')
                    ->label(__('console.schools.fields.address'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('region'))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('console.schools.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('npsn')
                    ->label(__('console.schools.fields.npsn'))
                    ->placeholder(__('console.schools.hints.npsn_missing'))
                    ->searchable(),
                TextColumn::make('level')
                    ->label(__('console.schools.fields.level'))
                    ->formatStateUsing(fn (SchoolLevel $state): string => $state->label())
                    ->badge(),
                TextColumn::make('region.regency')
                    ->label(__('console.schools.fields.region'))
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label(__('console.schools.fields.level'))
                    ->options(StaffAccess::options(SchoolLevel::class)),
            ])
            ->recordActions([
                EditAction::make(),
                // Deleting a school removes its memberships too (FK CASCADE); the modal names the school.
                DeleteAction::make()
                    ->modalDescription(fn (School $record): string => __('console.schools.delete_warning', ['name' => $record->name])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchools::route('/'),
        ];
    }
}
