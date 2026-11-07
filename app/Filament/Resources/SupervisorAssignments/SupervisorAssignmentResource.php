<?php

declare(strict_types=1);

namespace App\Filament\Resources\SupervisorAssignments;

use App\Enums\StaffRole;
use App\Filament\Resources\StaffAccess;
use App\Filament\Resources\SupervisorAssignments\Pages\ManageSupervisorAssignments;
use App\Models\SupervisorAssignment;
use App\Models\User;
use BackedEnum;
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
 * Which schools a pengawas or korwil may read (D-168). Not school-scoped data, and
 * managed only here.
 */
final class SupervisorAssignmentResource extends Resource
{
    protected static ?string $model = SupervisorAssignment::class;

    protected static ?string $slug = 'penugasan-pengawas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;

    public static function canAccess(): bool
    {
        return StaffAccess::allows(StaffRole::Admin, StaffRole::Support);
    }

    public static function getModelLabel(): string
    {
        return __('console.supervisor_assignments.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('console.supervisor_assignments.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('console.supervisor_assignments.fields.user'))
                    ->relationship('user', 'email')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} ({$record->email})")
                    ->searchable(['name', 'email'])
                    ->required(),
                Select::make('school_id')
                    ->label(__('console.supervisor_assignments.fields.school'))
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('started_on')
                    ->label(__('console.supervisor_assignments.fields.started_on'))
                    ->default(now())
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule
                            ->where('school_id', $get('school_id'))
                            ->where('user_id', $get('user_id')),
                    ),
                DatePicker::make('ended_on')
                    ->label(__('console.supervisor_assignments.fields.ended_on'))
                    ->afterOrEqual('started_on'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['school:id,name', 'user:id,name,email']))
            ->defaultSort('started_on', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('console.supervisor_assignments.fields.user'))
                    ->description(fn (SupervisorAssignment $record): string => $record->user->email)
                    ->searchable(['name', 'email']),
                TextColumn::make('school.name')
                    ->label(__('console.supervisor_assignments.fields.school'))
                    ->searchable(),
                TextColumn::make('started_on')
                    ->label(__('console.supervisor_assignments.fields.started_on'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('ended_on')
                    ->label(__('console.supervisor_assignments.fields.ended_on'))
                    ->date('d/m/Y')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('school')
                    ->label(__('console.supervisor_assignments.fields.school'))
                    ->relationship('school', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription(fn (SupervisorAssignment $record): string => __('console.supervisor_assignments.delete_warning', [
                        'name' => $record->user->name,
                        'school' => $record->school->name,
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSupervisorAssignments::route('/'),
        ];
    }
}
