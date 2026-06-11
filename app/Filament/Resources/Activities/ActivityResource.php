<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Resources\Activities\Tables\ActivitiesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 9;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.audit_log');
    }

    public static function getModelLabel(): string
    {
        return __('admin.audit_entry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.audit_log');
    }

    /**
     * The audit log is read-only — entries are written by the application, never by hand.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}
