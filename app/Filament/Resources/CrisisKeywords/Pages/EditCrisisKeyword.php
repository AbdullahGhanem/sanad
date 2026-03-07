<?php

namespace App\Filament\Resources\CrisisKeywords\Pages;

use App\Filament\Resources\CrisisKeywords\CrisisKeywordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCrisisKeyword extends EditRecord
{
    protected static string $resource = CrisisKeywordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
