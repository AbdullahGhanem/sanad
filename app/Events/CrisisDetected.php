<?php

namespace App\Events;

use App\Models\CrisisEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrisisDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CrisisEvent $crisisEvent,
        public ?string $language = null,
    ) {}

    public function source(): string
    {
        return $this->crisisEvent->source;
    }

    public function severity(): string
    {
        return $this->crisisEvent->severity ?? 'high';
    }
}
