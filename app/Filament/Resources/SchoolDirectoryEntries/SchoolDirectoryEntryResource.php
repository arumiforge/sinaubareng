<?php

declare(strict_types=1);

namespace App\Filament\Resources\SchoolDirectoryEntries;

use App\Enums\SchoolLevel;
use App\Enums\StaffRole;
use App\Filament\Resources\SchoolDirectoryEntries\Pages\ManageSchoolDirectoryEntries;
use App\Filament\Resources\StaffAccess;
use App\Models\SchoolDirectoryEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Where SIGRAK already has schools and which schools to reach next (D-170). Read-only:
 * the rows come from `school-directory:import`.
 */
final class SchoolDirectoryEntryResource extends Resource
{
    protected static ?string $model = SchoolDirectoryEntry::class;

    protected static ?string $slug = 'sebaran-sekolah';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Support);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getModelLabel(): string
    {
        return __('console.directory.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.directory.plural_label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            // shortcut: a customer is a school created in the console; switch to an active subscription once billing exists (P5-10).
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('region')->withExists('school'))
            ->defaultSort('district')
            ->defaultGroup('district')
            // "Ringkasan" shows one row per group with its counts instead of every school.
            ->groupsOnly(fn (ManageSchoolDirectoryEntries $livewire): bool => $livewire->summaryOnly)
            ->groups([
                Group::make('district')->label(__('console.directory.fields.district'))->collapsible(),
                Group::make('region.regency')->label(__('console.directory.fields.region'))->collapsible(),
                Group::make('level')
                    ->label(__('console.directory.fields.level'))
                    ->getTitleFromRecordUsing(fn (SchoolDirectoryEntry $record): string => $record->level->label()),
            ])
            ->columns([
                TextColumn::make('npsn')
                    ->label(__('console.directory.fields.npsn'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('console.directory.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->summarize(Count::make()->label(__('console.directory.summary.schools'))),
                TextColumn::make('level')
                    ->label(__('console.directory.fields.level'))
                    ->formatStateUsing(fn (SchoolLevel $state): string => $state->label())
                    ->badge(),
                TextColumn::make('district')
                    ->label(__('console.directory.fields.district'))
                    ->sortable(),
                TextColumn::make('region.regency')
                    ->label(__('console.directory.fields.region')),
                TextColumn::make('school_exists')
                    ->label(__('console.directory.fields.is_customer'))
                    ->formatStateUsing(fn (bool $state): string => $state ? __('console.directory.filters.customers') : __('console.directory.filters.not_customers'))
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->summarize(Sum::make()->label(__('console.directory.summary.customers'))),
            ])
            ->filters([
                SelectFilter::make('region')
                    ->label(__('console.directory.fields.region'))
                    ->relationship('region', 'regency'),
                SelectFilter::make('district')
                    ->label(__('console.directory.fields.district'))
                    ->options(fn (): array => SchoolDirectoryEntry::query()->distinct()->orderBy('district')->pluck('district', 'district')->all())
                    ->searchable(),
                SelectFilter::make('level')
                    ->label(__('console.directory.fields.level'))
                    ->options(StaffAccess::options(SchoolLevel::class)),
                TernaryFilter::make('is_customer')
                    ->label(__('console.directory.fields.is_customer'))
                    ->trueLabel(__('console.directory.filters.customers'))
                    ->falseLabel(__('console.directory.filters.not_customers'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->has('school'),
                        false: fn (Builder $query): Builder => $query->doesntHave('school'),
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchoolDirectoryEntries::route('/'),
        ];
    }
}
