<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsentRecord>
 */
class ConsentRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anonymous_id' => fake()->uuid(),
            'version' => '1.0',
            'locale' => fake()->randomElement(['en', 'ar']),
            'ip_address' => fake()->ipv4(),
            'agreed_at' => now(),
        ];
    }
}
