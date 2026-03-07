<?php

namespace App\Filament\Resources\Recommendations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecommendationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_ar')
                    ->searchable(),
                TextColumn::make('title_en')
                    ->searchable(),
                TextColumn::make('url')
                    ->searchable(),
                TextColumn::make('min_phq9')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_phq9')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('min_gad7')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_gad7')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('language')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
