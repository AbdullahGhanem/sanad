<?php

namespace App\Filament\Resources\AiProviderSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AiProviderSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider')
                    ->options([
                        'openai' => 'OpenAI',
                        'anthropic' => 'Anthropic',
                        'gemini' => 'Google Gemini',
                        'azure' => 'Azure OpenAI',
                    ])
                    ->required(),
                TextInput::make('model')
                    ->required()
                    ->placeholder('e.g. gpt-4o, claude-sonnet-4-20250514'),
                TextInput::make('api_key')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->label('API Key'),
                Toggle::make('is_active')
                    ->default(false)
                    ->label('Active'),
            ]);
    }
}
