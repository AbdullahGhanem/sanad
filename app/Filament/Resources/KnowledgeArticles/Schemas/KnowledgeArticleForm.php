<?php

namespace App\Filament\Resources\KnowledgeArticles\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KnowledgeArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article')
                    ->description('Embeddings regenerate automatically in the background when the title or body changes.')
                    ->components([
                        TextInput::make('category')
                            ->helperText('e.g. depression, anxiety, sleep, stress'),
                        TextInput::make('title_en')
                            ->required()
                            ->label('Title (English)'),
                        TextInput::make('title_ar')
                            ->required()
                            ->label('Title (Arabic)'),
                        Textarea::make('body_en')
                            ->required()
                            ->rows(6)
                            ->label('Body (English)'),
                        Textarea::make('body_ar')
                            ->required()
                            ->rows(6)
                            ->label('Body (Arabic)'),
                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),
                    ]),
            ]);
    }
}
