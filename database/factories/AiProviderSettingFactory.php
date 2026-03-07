<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiProviderSetting>
 */
class AiProviderSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider' => fake()->randomElement(['openai', 'gemini', 'azure']),
            'model' => 'gpt-4o',
            'api_key' => fake()->sha256(),
            'is_active' => false,
        ];
    }
}
