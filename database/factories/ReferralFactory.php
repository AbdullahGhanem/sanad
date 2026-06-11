<?php

namespace Database\Factories;

use App\Models\CrisisEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
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
            'anonymous_id' => fake()->uuid(),
            'referred_by_id' => User::factory()->counselor(),
            'status' => 'pending',
            'reason' => fake()->sentence(),
            'scheduled_at' => null,
        ];
    }
}
