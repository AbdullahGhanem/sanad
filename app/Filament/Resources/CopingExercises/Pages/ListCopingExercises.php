<?php

namespace App\Filament\Resources\CopingExercises\Pages;

use App\Filament\Resources\CopingExercises\CopingExerciseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCopingExercises extends ListRecords
{
    protected static string $resource = CopingExerciseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
