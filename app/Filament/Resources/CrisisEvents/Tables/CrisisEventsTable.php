<?php

namespace App\Filament\Resources\CrisisEvents\Tables;

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
            ]);
    }
}
