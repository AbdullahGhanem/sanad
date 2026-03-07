<?php

namespace App\Filament\Resources\AiProviderSettings\Pages;

use App\Filament\Resources\AiProviderSettings\AiProviderSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiProviderSetting extends EditRecord
{
    protected static string $resource = AiProviderSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
