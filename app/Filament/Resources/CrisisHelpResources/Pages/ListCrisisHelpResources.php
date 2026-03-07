<?php

namespace App\Filament\Resources\CrisisHelpResources\Pages;

use App\Filament\Resources\CrisisHelpResources\CrisisHelpResourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrisisHelpResources extends ListRecords
{
    protected static string $resource = CrisisHelpResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
