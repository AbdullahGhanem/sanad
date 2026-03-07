<?php

namespace App\Filament\Resources\Questionnaires\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionnaireForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Questionnaire Details')
                    ->schema([
                        TextInput::make('name_en')
                            ->required()
                            ->label('Name (English)'),
                        TextInput::make('name_ar')
                            ->required()
                            ->label('Name (Arabic)'),
                        Select::make('type')
                            ->options([
                                'phq9' => 'PHQ-9 (Depression)',
                                'gad7' => 'GAD-7 (Anxiety)',
                            ])
                            ->required(),
                        TextInput::make('version')
                            ->required()
                            ->numeric()
                            ->default(1),
                        DateTimePicker::make('published_at')
                            ->label('Published At')
                            ->nullable(),
                    ])->columns(2),

                Section::make('Questions')
                    ->schema([
                        Repeater::make('questions')
                            ->relationship()
                            ->schema([
                                TextInput::make('text_en')
                                    ->required()
                                    ->label('Question (English)')
                                    ->columnSpanFull(),
                                TextInput::make('text_ar')
                                    ->required()
                                    ->label('Question (Arabic)')
                                    ->columnSpanFull(),
                                TextInput::make('order')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                TextInput::make('min_score')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                TextInput::make('max_score')
                                    ->required()
                                    ->numeric()
                                    ->default(3),
                                Repeater::make('options')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('label_en')
                                            ->required()
                                            ->label('Label (English)'),
                                        TextInput::make('label_ar')
                                            ->required()
                                            ->label('Label (Arabic)'),
                                        TextInput::make('value')
                                            ->required()
                                            ->numeric(),
                                        TextInput::make('order')
                                            ->required()
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull()
                                    ->defaultItems(4)
                                    ->label('Answer Options'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible()
                            ->orderColumn('order')
                            ->label(''),
                    ]),
            ]);
    }
}
