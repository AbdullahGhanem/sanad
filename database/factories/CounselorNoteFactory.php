<?php

namespace Database\Factories;

use App\Models\CrisisEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CounselorNote>
 */
class CounselorNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crisis_event_id' => CrisisEvent::factory(),
            'user_id' => User::factory()->counselor(),
            'body' => fake()->sentence(),
        ];
    }
}
