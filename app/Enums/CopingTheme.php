<?php

namespace App\Enums;

enum CopingTheme: string
{
    case Anxiety = 'anxiety';
    case Sadness = 'sadness';
    case Stress = 'stress';
    case Anger = 'anger';
    case Loneliness = 'loneliness';
    case Hopelessness = 'hopelessness';
    case General = 'general';

    /**
     * Get a human-readable label for the theme.
     */
    public function label(): string
    {
        return ucfirst($this->value);
    }

    /**
     * Get the theme values keyed for a Filament select component.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $theme): array => [$theme->value => $theme->label()])
            ->all();
    }

    /**
     * Get every theme value.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
