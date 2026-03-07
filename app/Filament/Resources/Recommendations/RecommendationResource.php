<?php

namespace App\Filament\Resources\Recommendations;

use App\Filament\Resources\Recommendations\Pages\CreateRecommendation;
use App\Filament\Resources\Recommendations\Pages\EditRecommendation;
use App\Filament\Resources\Recommendations\Pages\ListRecommendations;
use App\Filament\Resources\Recommendations\Schemas\RecommendationForm;
use App\Filament\Resources\Recommendations\Tables\RecommendationsTable;
use App\Models\Recommendation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RecommendationResource extends Resource
{
    protected static ?string $model = Recommendation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_content');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.recommendations');
    }

    public static function getModelLabel(): string
    {
        return __('admin.recommendation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.recommendations');
    }

    public static function form(Schema $schema): Schema
    {
        return RecommendationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecommendationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecommendations::route('/'),
            'create' => CreateRecommendation::route('/create'),
            'edit' => EditRecommendation::route('/{record}/edit'),
        ];
    }
}
