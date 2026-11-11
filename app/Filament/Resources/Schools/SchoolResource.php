<?php

declare(strict_types=1);

namespace App\Filament\Resources\Schools;

use App\Enums\SchoolLevel;
use App\Enums\StaffRole;
use App\Filament\Resources\Schools\Pages\ManageSchools;
use App\Filament\Resources\StaffAccess;
use App\Models\Region;
use App\Models\School;
use App\Models\SchoolDirectoryEntry;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, Get $get, Set $set) => self::fillFromDirectory($state, $get, $set)),
                Select::make('level')
                    ->label(__('console.schools.fields.level'))
                    ->options(StaffAccess::options(SchoolLevel::class))
                    ->required(),
                Select::make('region_id')
                    ->label(__('console.schools.fields.region'))
                    ->relationship('region', 'regency', fn (Builder $query): Builder => $query->whereNotNull('regency')->orderBy('province')->orderBy('regency'))
                    ->getOptionLabelFromRecordUsing(fn (Region $record): string => "{$record->regency}, {$record->province}")
                    ->searchable(['province', 'regency'])
                    ->preload()
                    ->required(),
                Textarea::make('address')
                    ->label(__('console.schools.fields.address'))
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Fills empty fields from the school directory when the NPSN is found there (D-169).
     */
    private static function fillFromDirectory(?string $npsn, Get $get, Set $set): void
    {
        $entry = filled($npsn) ? SchoolDirectoryEntry::query()->firstWhere('npsn', $npsn) : null;

        if ($entry === null) {
            return;
        }

        $values = [
            'name' => $entry->name,
            'level' => $entry->level->value,
            'region_id' => $entry->region_id,
            'address' => $entry->fullAddress(),
        ];

        foreach ($values as $field => $value) {
            if (blank($get($field))) {
                $set($field, $value);
            }
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('region')->withExists('directoryEntry'))
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
                // Shows which schools the Sebaran sekolah page can count (D-170).
                TextColumn::make('directory_match')
                    ->label(__('console.schools.fields.directory_match'))
                    ->state(fn (School $record): string => match (true) {
                        $record->npsn === null => 'missing_npsn',
                        (bool) $record->getAttribute('directory_entry_exists') => 'matched',
                        default => 'not_in_directory',
                    })
                    ->formatStateUsing(fn (string $state): string => __("console.schools.directory_match.{$state}"))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'matched' => 'success',
                        'missing_npsn' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label(__('console.schools.fields.level'))
                    ->options(StaffAccess::options(SchoolLevel::class)),
                SelectFilter::make('directory_match')
                    ->label(__('console.schools.fields.directory_match'))
                    ->options([
                        'matched' => __('console.schools.directory_match.matched'),
                        'missing_npsn' => __('console.schools.directory_match.missing_npsn'),
                        'not_in_directory' => __('console.schools.directory_match.not_in_directory'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'matched' => $query->has('directoryEntry'),
                        'missing_npsn' => $query->whereNull('npsn'),
                        'not_in_directory' => $query->whereNotNull('npsn')->doesntHave('directoryEntry'),
                        default => $query,
                    }),
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
