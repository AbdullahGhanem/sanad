<?php

namespace App\Filament\Resources\CrisisHelpResources\Pages;

use App\Filament\Resources\CrisisHelpResources\CrisisHelpResourceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCrisisHelpResource extends EditRecord
{
    protected static string $resource = CrisisHelpResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
