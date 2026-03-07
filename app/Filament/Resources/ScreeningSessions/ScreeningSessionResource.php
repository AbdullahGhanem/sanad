<?php

namespace App\Filament\Resources\ScreeningSessions;

use App\Filament\Resources\ScreeningSessions\Pages\ListScreeningSessions;
use App\Filament\Resources\ScreeningSessions\Tables\ScreeningSessionsTable;
use App\Models\ScreeningSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScreeningSessionResource extends Resource
{
    protected static ?string $model = ScreeningSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_screening');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.sessions');
    }

    public static function getModelLabel(): string
    {
        return __('admin.session');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.sessions');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return ScreeningSessionsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScreeningSessions::route('/'),
        ];
    }
}
