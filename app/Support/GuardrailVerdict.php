<?php

namespace App\Support;

final class GuardrailVerdict
{
    /**
     * @param  list<string>  $categories
     */
    public function __construct(
        public bool $safe,
        public string $source,
        public array $categories = [],
        public ?string $reason = null,
    ) {}

    public static function safe(string $source): self
    {
        return new self(true, $source);
    }

    /**
     * @param  list<string>  $categories
     */
    public static function unsafe(string $source, array $categories = [], ?string $reason = null): self
    {
        return new self(false, $source, $categories, $reason);
    }

    public function blocked(): bool
    {
        return ! $this->safe;
    }
}
