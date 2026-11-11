<?php

declare(strict_types=1);

namespace App\Filament\Resources\NationalHolidays;

use App\Enums\NationalHolidayKind;
use App\Enums\StaffRole;
use App\Filament\Resources\NationalHolidays\Pages\ManageNationalHolidays;
use App\Filament\Resources\StaffAccess;
use App\Models\NationalHoliday;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Libur nasional (D-253): Tim SIGRAK keeps national holidays and cuti bersama here when
 * a new SKB is out; `calendar:import-holidays` loads a whole file.
 */
final class NationalHolidayResource extends Resource
{
    protected static ?string $model = NationalHoliday::class;

    protected static ?string $slug = 'libur-nasional';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Content);
    }

    public static function getModelLabel(): string
    {
        return __('console.national_holidays.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.national_holidays.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label(__('console.national_holidays.fields.date'))
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label(__('console.national_holidays.fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('kind')
                    ->label(__('console.national_holidays.fields.kind'))
                    ->options(StaffAccess::options(NationalHolidayKind::class))
                    ->required(),
                TextInput::make('academic_year')
                    ->label(__('console.national_holidays.fields.academic_year'))
                    ->required()
                    ->regex('/^\d{4}\/\d{4}$/')
                    ->placeholder('2026/2027'),
                TextInput::make('decree')
                    ->label(__('console.national_holidays.fields.decree'))
                    ->maxLength(255),
                Toggle::make('is_provisional')
                    ->label(__('console.national_holidays.fields.is_provisional')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date')
            ->columns([
                TextColumn::make('date')->label(__('console.national_holidays.fields.date'))->date('d/m/Y')->sortable(),
                TextColumn::make('name')->label(__('console.national_holidays.fields.name'))->searchable(),
                TextColumn::make('kind')->label(__('console.national_holidays.fields.kind'))
                    ->formatStateUsing(fn (NationalHolidayKind $state): string => $state->label()),
                TextColumn::make('academic_year')->label(__('console.national_holidays.fields.academic_year')),
                IconColumn::make('is_provisional')->label(__('console.national_holidays.fields.is_provisional'))->boolean(),
            ])
            ->filters([
                SelectFilter::make('academic_year')
                    ->label(__('console.national_holidays.fields.academic_year'))
                    ->options(fn (): array => NationalHoliday::query()->distinct()->orderBy('academic_year')->pluck('academic_year', 'academic_year')->all()),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNationalHolidays::route('/'),
        ];
    }
}
