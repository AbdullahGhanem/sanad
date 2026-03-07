<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->maxLength(255)
                    ->label('Password'),
                Select::make('role')
                    ->options([
                        'student' => 'Student',
                        'university_admin' => 'University Admin',
                        'super_admin' => 'Super Admin',
                    ])
                    ->required()
                    ->default('student'),
                TextInput::make('faculty_id')
                    ->numeric()
                    ->label('Faculty ID'),
                Toggle::make('reminder_enabled')
                    ->default(true)
                    ->label('Screening Reminders'),
            ]);
    }
}
