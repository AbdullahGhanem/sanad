<?php

namespace App\Filament\Resources\KnowledgeArticles;

use App\Filament\Resources\KnowledgeArticles\Pages\CreateKnowledgeArticle;
use App\Filament\Resources\KnowledgeArticles\Pages\EditKnowledgeArticle;
use App\Filament\Resources\KnowledgeArticles\Pages\ListKnowledgeArticles;
use App\Filament\Resources\KnowledgeArticles\Schemas\KnowledgeArticleForm;
use App\Filament\Resources\KnowledgeArticles\Tables\KnowledgeArticlesTable;
use App\Models\KnowledgeArticle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KnowledgeArticleResource extends Resource
{
    protected static ?string $model = KnowledgeArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_content');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.knowledge_articles');
    }

    public static function getModelLabel(): string
    {
        return __('admin.knowledge_article');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.knowledge_articles');
    }

    public static function form(Schema $schema): Schema
    {
        return KnowledgeArticleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeArticlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeArticles::route('/'),
            'create' => CreateKnowledgeArticle::route('/create'),
            'edit' => EditKnowledgeArticle::route('/{record}/edit'),
        ];
    }
}
