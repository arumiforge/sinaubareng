<?php

declare(strict_types=1);

namespace App\Filament\Resources\RegionCalendars;

use App\Enums\CalendarStatus;
use App\Enums\StaffRole;
use App\Filament\Resources\RegionCalendars\Pages\EditRegionCalendar;
use App\Filament\Resources\RegionCalendars\Pages\ListRegionCalendars;
use App\Filament\Resources\RegionCalendars\RelationManagers\EventsRelationManager;
use App\Filament\Resources\StaffAccess;
use App\Models\Region;
use App\Models\RegionCalendar;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Kalender wilayah (D-256, D-257, D-323): reference calendars built from the dinas kaldik.
 * Every change raises the version, so schools that copied it see "Terapkan perubahan
 * wilayah". Draft until Tim Validasi signs it off; schools only see active calendars.
 */
final class RegionCalendarResource extends Resource
{
    protected static ?string $model = RegionCalendar::class;

    protected static ?string $slug = 'kalender-wilayah';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Content);
    }

    public static function getModelLabel(): string
    {
        return __('console.region_calendars.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.region_calendars.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('region_id')
                    ->label(__('console.region_calendars.fields.region'))
                    ->relationship('region', 'province', fn (Builder $query): Builder => $query->orderBy('province')->orderBy('regency'))
                    ->getOptionLabelFromRecordUsing(fn (Region $record): string => $record->regency === null ? __('console.region_calendars.province', ['province' => $record->province]) : "{$record->regency}, {$record->province}")
                    ->searchable(['province', 'regency'])
                    ->preload()
                    ->required(),
                TextInput::make('academic_year')
                    ->label(__('console.region_calendars.fields.academic_year'))
                    ->required()
                    ->regex('/^\d{4}\/\d{4}$/')
                    ->placeholder('2026/2027'),
                TextInput::make('code')->label(__('console.region_calendars.fields.code'))->required()->maxLength(64)->unique(ignoreRecord: true),
                TextInput::make('name')->label(__('console.region_calendars.fields.name'))->required()->maxLength(255),
                Select::make('status')
                    ->label(__('console.region_calendars.fields.status'))
                    ->options(StaffAccess::options(CalendarStatus::class))
                    ->default(CalendarStatus::Draft->value)
                    ->required(),
                Select::make('school_days_per_week')
                    ->label(__('console.region_calendars.fields.school_days_per_week'))
                    ->options([5 => '5', 6 => '6'])
                    ->required(),
                DatePicker::make('starts_on')->label(__('console.region_calendars.fields.starts_on'))->required(),
                DatePicker::make('even_semester_starts_on')->label(__('console.region_calendars.fields.even_semester_starts_on'))->required()->after('starts_on'),
                DatePicker::make('ends_on')->label(__('console.region_calendars.fields.ends_on'))->required()->after('even_semester_starts_on'),
                TagsInput::make('joint_leave_school_days')
                    ->label(__('console.region_calendars.fields.joint_leave_school_days'))
                    ->helperText(__('console.region_calendars.hints.joint_leave_school_days'))
                    ->nestedRecursiveRules(['date_format:Y-m-d']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('academic_year', 'desc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('region')->withCount('events'))
            ->columns([
                TextColumn::make('name')->label(__('console.region_calendars.fields.name'))->searchable(),
                TextColumn::make('region.province')->label(__('console.region_calendars.fields.region'))
                    ->formatStateUsing(fn (RegionCalendar $record): string => $record->region->regency ?? __('console.region_calendars.province', ['province' => $record->region->province])),
                TextColumn::make('academic_year')->label(__('console.region_calendars.fields.academic_year'))->sortable(),
                TextColumn::make('status')->label(__('console.region_calendars.fields.status'))
                    ->formatStateUsing(fn (CalendarStatus $state): string => $state->label())
                    ->badge(),
                TextColumn::make('version')->label(__('console.region_calendars.fields.version'))->numeric(),
                TextColumn::make('events_count')->label(__('console.region_calendars.fields.events_count'))->numeric(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegionCalendars::route('/'),
            'edit' => EditRegionCalendar::route('/{record}/ubah'),
        ];
    }
}
