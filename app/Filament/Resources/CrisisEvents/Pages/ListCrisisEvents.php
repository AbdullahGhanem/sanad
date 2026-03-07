<?php

namespace App\Filament\Resources\CrisisEvents\Pages;

use App\Filament\Resources\CrisisEvents\CrisisEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrisisEvents extends ListRecords
{
    protected static string $resource = CrisisEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
