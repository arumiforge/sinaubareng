<?php

declare(strict_types=1);

namespace App\Filament\Resources\Regions;

use App\Enums\StaffRole;
use App\Filament\Resources\Regions\Pages\ManageRegions;
use App\Filament\Resources\StaffAccess;
use App\Models\Region;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

final class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $slug = 'wilayah';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'regency';

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Support);
    }

    public static function getModelLabel(): string
    {
        return __('console.regions.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.regions.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('province')
                    ->label(__('console.regions.fields.province'))
                    ->required()
                    ->maxLength(255),
                // Empty for a province-level region, which owns the province's reference calendar (D-323).
                TextInput::make('regency')
                    ->label(__('console.regions.fields.regency'))
                    ->helperText(__('console.regions.hints.regency'))
                    ->nullable()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, callable $get): Unique => $rule->where('province', $get('province'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('regency')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount('schools'))
            ->defaultSort('province')
            ->columns([
                TextColumn::make('province')
                    ->label(__('console.regions.fields.province'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('regency')
                    ->label(__('console.regions.fields.regency'))
                    ->placeholder(__('console.regions.whole_province'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('schools_count')
                    ->label(__('console.regions.fields.schools_count'))
                    ->numeric(),
            ])
            ->recordActions([
                EditAction::make(),
                // A region with schools cannot be deleted (FK RESTRICT).
                DeleteAction::make()->hidden(fn (Region $record): bool => $record->schools_count > 0),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRegions::route('/'),
        ];
    }
}
