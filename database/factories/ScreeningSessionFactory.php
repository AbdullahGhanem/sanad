<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScreeningSession>
 */
class ScreeningSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'anonymous_id' => Str::uuid()->toString(),
            'phq9_score' => null,
            'gad7_score' => null,
            'combined_severity' => null,
            'nlp_classification' => null,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'phq9_score' => fake()->numberBetween(0, 27),
            'gad7_score' => fake()->numberBetween(0, 21),
            'combined_severity' => fake()->randomElement(['minimal', 'mild', 'moderate', 'moderately_severe', 'severe']),
            'completed_at' => now(),
        ]);
    }
}
