<?php

namespace App\Filament\Resources\CrisisEvents\Pages;

use App\Filament\Resources\CrisisEvents\CrisisEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCrisisEvent extends EditRecord
{
    protected static string $resource = CrisisEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
