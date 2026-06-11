<?php

namespace App\Filament\Resources\CrisisEvents\Tables;

use App\Enums\CrisisStatus;
use App\Models\CrisisEvent;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CrisisEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source')
                    ->badge()
                    ->searchable(),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'moderate' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CrisisStatus $state): string => $state->label())
                    ->color(fn (CrisisStatus $state): string => $state->color()),
                TextColumn::make('handledBy.name')
                    ->label('Handled by')
                    ->placeholder('—'),
                TextColumn::make('notes_count')
                    ->counts('notes')
                    ->label('Notes')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Occurred At'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('source')
                    ->options([
                        'screening' => 'Screening',
                        'chat' => 'Chat',
                    ]),
                SelectFilter::make('severity')
                    ->options([
                        'high' => 'High',
                        'moderate' => 'Moderate',
                    ]),
                SelectFilter::make('status')
                    ->options(CrisisStatus::options()),
            ])
            ->recordActions([
                Action::make('acknowledge')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (CrisisEvent $record): bool => $record->status === CrisisStatus::Open)
                    ->action(fn (CrisisEvent $record) => $record->acknowledge(auth()->user())),
                Action::make('resolve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (CrisisEvent $record): bool => ! $record->isResolved())
                    ->action(fn (CrisisEvent $record) => $record->resolve(auth()->user())),
                Action::make('addNote')
                    ->label('Add note')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->color('gray')
                    ->modalHeading('Counselor note')
                    ->schema([
                        Textarea::make('body')
                            ->label('Note')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(fn (array $data, CrisisEvent $record) => $record->notes()->create([
                        'user_id' => auth()->id(),
                        'body' => $data['body'],
                    ])),
            ]);
    }
}
