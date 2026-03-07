<?php

namespace Database\Factories;

use App\Models\CrisisHelpResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CrisisHelpResource>
 */
class CrisisHelpResourceFactory extends Factory
{
    protected $model = CrisisHelpResource::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['phone', 'website', 'facility']),
            'icon' => fake()->randomElement(['phone', 'globe', 'hospital']),
            'title_en' => fake()->words(3, true),
            'title_ar' => fake()->words(3, true),
            'value' => fake()->phoneNumber(),
            'detail_en' => fake()->sentence(),
            'detail_ar' => fake()->sentence(),
            'url' => fake()->optional()->url(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
