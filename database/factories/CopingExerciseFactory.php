<?php

namespace Database\Factories;

use App\Enums\CopingTheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CopingExercise>
 */
class CopingExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['breathing', 'grounding', 'cognitive', 'behavioral', 'mindfulness']),
            'themes' => fake()->randomElements(CopingTheme::values(), fake()->numberBetween(1, 3)),
            'title_en' => fake()->words(3, true),
            'title_ar' => fake()->words(3, true),
            'summary_en' => fake()->sentence(),
            'summary_ar' => fake()->sentence(),
            'steps_en' => fake()->paragraph(),
            'steps_ar' => fake()->paragraph(),
            'duration_minutes' => fake()->numberBetween(2, 15),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function forTheme(CopingTheme $theme): static
    {
        return $this->state(fn (array $attributes): array => [
            'themes' => [$theme->value],
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
