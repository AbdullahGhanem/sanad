<?php

namespace Database\Factories;

use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'questionnaire_id' => Questionnaire::factory(),
            'order' => fake()->numberBetween(0, 9),
            'text_ar' => fake()->sentence(),
            'text_en' => fake()->sentence(),
            'min_score' => 0,
            'max_score' => 3,
        ];
    }
}
