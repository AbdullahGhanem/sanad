<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Log')
                    ->badge()
                    ->searchable(),
                TextColumn::make('event')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—'),
                TextColumn::make('causer.name')
                    ->label('By')
                    ->default('System'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log')
                    ->options([
                        'crisis' => 'Crisis events',
                        'crisis-config' => 'Crisis config',
                        'content' => 'Content',
                        'ai-config' => 'AI config',
                        'user' => 'Users',
                        'guardrail' => 'Guardrail',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
