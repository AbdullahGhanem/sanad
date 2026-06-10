<?php

namespace App\Filament\Resources\CopingExercises;

use App\Filament\Resources\CopingExercises\Pages\CreateCopingExercise;
use App\Filament\Resources\CopingExercises\Pages\EditCopingExercise;
use App\Filament\Resources\CopingExercises\Pages\ListCopingExercises;
use App\Filament\Resources\CopingExercises\Schemas\CopingExerciseForm;
use App\Filament\Resources\CopingExercises\Tables\CopingExercisesTable;
use App\Models\CopingExercise;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CopingExerciseResource extends Resource
{
    protected static ?string $model = CopingExercise::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_content');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.coping_exercises');
    }

    public static function getModelLabel(): string
    {
        return __('admin.coping_exercise');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.coping_exercises');
    }

    public static function form(Schema $schema): Schema
    {
        return CopingExerciseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CopingExercisesTable::configure($table);
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
            'index' => ListCopingExercises::route('/'),
            'create' => CreateCopingExercise::route('/create'),
            'edit' => EditCopingExercise::route('/{record}/edit'),
        ];
    }
}
