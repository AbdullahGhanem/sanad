<?php

namespace App\Filament\Resources\CrisisKeywords\Pages;

use App\Filament\Resources\CrisisKeywords\CrisisKeywordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrisisKeywords extends ListRecords
{
    protected static string $resource = CrisisKeywordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
