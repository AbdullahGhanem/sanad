<?php

namespace App\Filament\Resources\CopingExercises\Schemas;

use App\Enums\CopingTheme;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CopingExerciseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Exercise Details')
                    ->components([
                        Select::make('type')
                            ->options([
                                'breathing' => 'Breathing',
                                'grounding' => 'Grounding',
                                'cognitive' => 'Cognitive',
                                'behavioral' => 'Behavioral',
                                'mindfulness' => 'Mindfulness',
                            ])
                            ->required(),
                        Select::make('themes')
                            ->multiple()
                            ->options(CopingTheme::options())
                            ->required()
                            ->helperText('Emotional themes this exercise helps with'),
                        TextInput::make('title_en')
                            ->required()
                            ->label('Title (English)'),
                        TextInput::make('title_ar')
                            ->required()
                            ->label('Title (Arabic)'),
                        TextInput::make('summary_en')
                            ->label('Summary (English)'),
                        TextInput::make('summary_ar')
                            ->label('Summary (Arabic)'),
                        Textarea::make('steps_en')
                            ->required()
                            ->rows(5)
                            ->label('Steps (English)'),
                        Textarea::make('steps_ar')
                            ->required()
                            ->rows(5)
                            ->label('Steps (Arabic)'),
                        TextInput::make('duration_minutes')
                            ->numeric()
                            ->minValue(1)
                            ->label('Duration (minutes)'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),
                    ]),
            ]);
    }
}
