<?php

declare(strict_types=1);

namespace App\Filament\Resources\RegionCalendars\RelationManagers;

use App\Enums\CalendarEventKind;
use App\Enums\ExamType;
use App\Filament\Resources\StaffAccess;
use App\Models\CalendarEvent;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Events of a region calendar; each save or delete raises its version (D-256). */
final class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label(__('calendar.fields.title'))->required()->maxLength(120),
                Select::make('kind')
                    ->label(__('calendar.fields.kind'))
                    ->options(StaffAccess::options(CalendarEventKind::class))
                    ->live()
                    ->required(),
                Select::make('exam_type')
                    ->label(__('calendar.fields.exam_type'))
                    ->options(StaffAccess::options(ExamType::class))
                    ->visible(fn (Get $get): bool => $get('kind') === CalendarEventKind::Exam->value || $get('kind') === CalendarEventKind::Exam)
                    ->required(fn (Get $get): bool => $get('kind') === CalendarEventKind::Exam->value || $get('kind') === CalendarEventKind::Exam),
                DatePicker::make('starts_on')->label(__('calendar.fields.starts_on'))->required(),
                DatePicker::make('ends_on')->label(__('calendar.fields.ends_on'))->required()->afterOrEqual('starts_on'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('starts_on')
            ->columns([
                TextColumn::make('starts_on')->label(__('calendar.fields.starts_on'))->date('d/m/Y')->sortable(),
                TextColumn::make('ends_on')->label(__('calendar.fields.ends_on'))->date('d/m/Y'),
                TextColumn::make('title')->label(__('calendar.fields.title'))->searchable(),
                TextColumn::make('kind')->label(__('calendar.fields.kind'))
                    ->formatStateUsing(fn (CalendarEvent $record): string => $record->exam_type?->label() ?? $record->kind->label()),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
