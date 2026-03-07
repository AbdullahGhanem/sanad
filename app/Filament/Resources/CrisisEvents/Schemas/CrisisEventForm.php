<?php

namespace App\Filament\Resources\CrisisEvents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CrisisEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('anonymous_id')
                    ->required(),
                TextInput::make('source')
                    ->required(),
                TextInput::make('severity'),
            ]);
    }
}
