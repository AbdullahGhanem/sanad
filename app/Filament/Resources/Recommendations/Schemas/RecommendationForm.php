<?php

namespace App\Filament\Resources\Recommendations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RecommendationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_en')
                    ->required()
                    ->label('Title (English)'),
                TextInput::make('title_ar')
                    ->required()
                    ->label('Title (Arabic)'),
                Textarea::make('body_en')
                    ->required()
                    ->label('Body (English)')
                    ->columnSpanFull(),
                Textarea::make('body_ar')
                    ->required()
                    ->label('Body (Arabic)')
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->url()
                    ->label('Resource URL'),
                TextInput::make('min_phq9')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(27)
                    ->default(0)
                    ->label('Min PHQ-9'),
                TextInput::make('max_phq9')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(27)
                    ->default(27)
                    ->label('Max PHQ-9'),
                TextInput::make('min_gad7')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(21)
                    ->default(0)
                    ->label('Min GAD-7'),
                TextInput::make('max_gad7')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(21)
                    ->default(21)
                    ->label('Max GAD-7'),
                Select::make('language')
                    ->options([
                        'both' => 'Both',
                        'en' => 'English',
                        'ar' => 'Arabic',
                    ])
                    ->default('both')
                    ->required(),
            ]);
    }
}
