<?php

namespace App\Filament\Resources\KnowledgeArticles\Tables;

use App\Models\KnowledgeArticle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class KnowledgeArticlesTable
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
                TextColumn::make('category')
                    ->badge(),
                IconColumn::make('embedded')
                    ->label('Indexed')
                    ->boolean()
                    ->state(fn (KnowledgeArticle $record): bool => $record->embedding_en !== null),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
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
