<?php

namespace App\Filament\Resources\CrisisKeywords;

use App\Filament\Resources\CrisisKeywords\Pages\CreateCrisisKeyword;
use App\Filament\Resources\CrisisKeywords\Pages\EditCrisisKeyword;
use App\Filament\Resources\CrisisKeywords\Pages\ListCrisisKeywords;
use App\Filament\Resources\CrisisKeywords\Schemas\CrisisKeywordForm;
use App\Filament\Resources\CrisisKeywords\Tables\CrisisKeywordsTable;
use App\Models\CrisisKeyword;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CrisisKeywordResource extends Resource
{
    protected static ?string $model = CrisisKeyword::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_crisis_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.crisis_keywords');
    }

    public static function getModelLabel(): string
    {
        return __('admin.crisis_keyword');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.crisis_keywords');
    }

    public static function form(Schema $schema): Schema
    {
        return CrisisKeywordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrisisKeywordsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrisisKeywords::route('/'),
            'create' => CreateCrisisKeyword::route('/create'),
            'edit' => EditCrisisKeyword::route('/{record}/edit'),
        ];
    }
}
