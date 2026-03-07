<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CrisisEvent>
 */
class CrisisEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anonymous_id' => Str::uuid()->toString(),
            'source' => fake()->randomElement(['screening', 'chat']),
            'severity' => fake()->randomElement(['moderate', 'high', 'critical']),
        ];
    }
}
