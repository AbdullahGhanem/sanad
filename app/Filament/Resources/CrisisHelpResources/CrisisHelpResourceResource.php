<?php

namespace App\Filament\Resources\CrisisHelpResources;

use App\Filament\Resources\CrisisHelpResources\Pages\CreateCrisisHelpResource;
use App\Filament\Resources\CrisisHelpResources\Pages\EditCrisisHelpResource;
use App\Filament\Resources\CrisisHelpResources\Pages\ListCrisisHelpResources;
use App\Filament\Resources\CrisisHelpResources\Schemas\CrisisHelpResourceForm;
use App\Filament\Resources\CrisisHelpResources\Tables\CrisisHelpResourcesTable;
use App\Models\CrisisHelpResource;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CrisisHelpResourceResource extends Resource
{
    protected static ?string $model = CrisisHelpResource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_crisis_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.help_resources');
    }

    public static function getModelLabel(): string
    {
        return __('admin.help_resource');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.help_resources');
    }

    public static function form(Schema $schema): Schema
    {
        return CrisisHelpResourceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrisisHelpResourcesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrisisHelpResources::route('/'),
            'create' => CreateCrisisHelpResource::route('/create'),
            'edit' => EditCrisisHelpResource::route('/{record}/edit'),
        ];
    }
}
