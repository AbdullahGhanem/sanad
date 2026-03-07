<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CrisisKeyword>
 */
class CrisisKeywordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phrase' => fake()->words(2, true),
            'language' => fake()->randomElement(['ar', 'en']),
            'is_active' => true,
        ];
    }
}
