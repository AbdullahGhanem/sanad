<?php

namespace App\Filament\Resources\CopingExercises\Tables;

use App\Enums\CopingTheme;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CopingExercisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable(),
                TextColumn::make('title_ar')
                    ->label('Title (AR)')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('themes')
                    ->badge()
                    ->separator(','),
                TextColumn::make('duration_minutes')
                    ->label('Min')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('themes')
                    ->options(CopingTheme::options())
                    ->query(fn ($query, array $data) => filled($data['value'] ?? null)
                        ? $query->whereJsonContains('themes', $data['value'])
                        : $query),
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
