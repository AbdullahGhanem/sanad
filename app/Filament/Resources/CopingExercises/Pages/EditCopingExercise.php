<?php

namespace App\Filament\Resources\CopingExercises\Pages;

use App\Filament\Resources\CopingExercises\CopingExerciseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCopingExercise extends EditRecord
{
    protected static string $resource = CopingExerciseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
