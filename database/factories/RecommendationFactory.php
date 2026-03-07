<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recommendation>
 */
class RecommendationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title_ar' => fake()->sentence(),
            'title_en' => fake()->sentence(),
            'body_ar' => fake()->paragraph(),
            'body_en' => fake()->paragraph(),
            'url' => fake()->optional()->url(),
            'min_phq9' => 0,
            'max_phq9' => 27,
            'min_gad7' => 0,
            'max_gad7' => 21,
            'language' => 'both',
        ];
    }
}
