<?php

namespace App\Filament\Resources\CrisisEvents;

use App\Filament\Resources\CrisisEvents\Pages\ListCrisisEvents;
use App\Filament\Resources\CrisisEvents\Tables\CrisisEventsTable;
use App\Models\CrisisEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CrisisEventResource extends Resource
{
    protected static ?string $model = CrisisEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_crisis_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.crisis_events');
    }

    public static function getModelLabel(): string
    {
        return __('admin.crisis_event');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.crisis_events');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return CrisisEventsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrisisEvents::route('/'),
        ];
    }
}
