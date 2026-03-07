<?php

namespace App\Filament\Resources\AiProviderSettings;

use App\Filament\Resources\AiProviderSettings\Pages\CreateAiProviderSetting;
use App\Filament\Resources\AiProviderSettings\Pages\EditAiProviderSetting;
use App\Filament\Resources\AiProviderSettings\Pages\ListAiProviderSettings;
use App\Filament\Resources\AiProviderSettings\Schemas\AiProviderSettingForm;
use App\Filament\Resources\AiProviderSettings\Tables\AiProviderSettingsTable;
use App\Models\AiProviderSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AiProviderSettingResource extends Resource
{
    protected static ?string $model = AiProviderSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.ai_settings');
    }

    public static function getModelLabel(): string
    {
        return __('admin.ai_setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.ai_settings');
    }

    public static function form(Schema $schema): Schema
    {
        return AiProviderSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiProviderSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiProviderSettings::route('/'),
            'create' => CreateAiProviderSetting::route('/create'),
            'edit' => EditAiProviderSetting::route('/{record}/edit'),
        ];
    }
}
