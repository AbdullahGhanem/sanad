<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ScreeningSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SessionAnswer>
 */
class SessionAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'screening_session_id' => ScreeningSession::factory(),
            'question_id' => Question::factory(),
            'question_option_id' => QuestionOption::factory(),
            'free_text' => null,
        ];
    }
}
