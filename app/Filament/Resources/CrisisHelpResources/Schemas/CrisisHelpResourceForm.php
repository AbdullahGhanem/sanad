<?php

namespace App\Filament\Resources\CrisisHelpResources\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CrisisHelpResourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Resource Details')
                    ->components([
                        Select::make('type')
                            ->options([
                                'phone' => 'Phone / Hotline',
                                'website' => 'Website',
                                'facility' => 'Facility / Center',
                            ])
                            ->required(),
                        Select::make('icon')
                            ->options([
                                'phone' => '📞 Phone',
                                'globe' => '🌐 Globe / Website',
                                'hospital' => '🏥 Hospital / Facility',
                                'chat' => '💬 Chat',
                                'email' => '📧 Email',
                            ])
                            ->required(),
                        TextInput::make('title_en')
                            ->required()
                            ->label('Title (English)'),
                        TextInput::make('title_ar')
                            ->required()
                            ->label('Title (Arabic)'),
                        TextInput::make('value')
                            ->required()
                            ->helperText('Phone number, website domain, or address'),
                        TextInput::make('detail_en')
                            ->label('Detail (English)'),
                        TextInput::make('detail_ar')
                            ->label('Detail (Arabic)'),
                        TextInput::make('url')
                            ->url()
                            ->label('URL')
                            ->helperText('Full URL for clickable links (e.g. https://nefsy.com)'),
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
