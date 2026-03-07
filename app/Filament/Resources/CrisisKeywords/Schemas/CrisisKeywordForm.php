<?php

namespace App\Filament\Resources\CrisisKeywords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CrisisKeywordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('phrase')
                    ->required()
                    ->label('Crisis Phrase'),
                Select::make('language')
                    ->options([
                        'en' => 'English',
                        'ar' => 'Arabic',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ]);
    }
}
