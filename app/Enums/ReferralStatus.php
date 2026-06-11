<?php

namespace App\Enums;

enum ReferralStatus: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Declined = 'declined';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Scheduled => 'info',
            self::Completed => 'success',
            self::Declined => 'gray',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }
}
