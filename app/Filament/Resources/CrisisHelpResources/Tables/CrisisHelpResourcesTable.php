<?php

namespace App\Filament\Resources\CrisisHelpResources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CrisisHelpResourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'phone' => 'danger',
                        'website' => 'info',
                        'facility' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable(),
                TextColumn::make('title_ar')
                    ->label('Title (AR)')
                    ->searchable(),
                TextColumn::make('value')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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
