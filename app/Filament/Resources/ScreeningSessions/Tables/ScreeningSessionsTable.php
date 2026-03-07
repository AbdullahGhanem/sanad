<?php

namespace App\Filament\Resources\ScreeningSessions\Tables;

use App\Filament\Exports\ScreeningSessionExporter;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScreeningSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('anonymous_id')
                    ->searchable()
                    ->label('Anonymous ID')
                    ->limit(12),
                TextColumn::make('phq9_score')
                    ->numeric()
                    ->sortable()
                    ->label('PHQ-9'),
                TextColumn::make('gad7_score')
                    ->numeric()
                    ->sortable()
                    ->label('GAD-7'),
                TextColumn::make('combined_severity')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'minimal' => 'gray',
                        'mild' => 'info',
                        'moderate' => 'warning',
                        'moderately_severe' => 'orange',
                        'severe' => 'danger',
                        default => 'gray',
                    })
                    ->label('Severity'),
                TextColumn::make('nlp_classification')
                    ->placeholder('N/A')
                    ->label('NLP')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('In Progress'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('combined_severity')
                    ->options([
                        'minimal' => 'Minimal',
                        'mild' => 'Mild',
                        'moderate' => 'Moderate',
                        'moderately_severe' => 'Moderately Severe',
                        'severe' => 'Severe',
                    ])
                    ->label('Severity'),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ScreeningSessionExporter::class),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
