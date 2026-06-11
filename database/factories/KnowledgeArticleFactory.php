<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KnowledgeArticle>
 */
class KnowledgeArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(['depression', 'anxiety', 'sleep', 'stress', 'relationships']),
            'title_en' => fake()->sentence(4),
            'title_ar' => fake()->sentence(4),
            'body_en' => fake()->paragraph(),
            'body_ar' => fake()->paragraph(),
            'embedding_en' => null,
            'embedding_ar' => null,
            'embedding_model' => null,
            'is_active' => true,
        ];
    }

    /**
     * Provide explicit embedding vectors (bypasses the async embedding job).
     *
     * @param  list<float>  $en
     * @param  list<float>|null  $ar
     */
    public function withEmbedding(array $en, ?array $ar = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'embedding_en' => $en,
            'embedding_ar' => $ar ?? $en,
            'embedding_model' => 'voyage-4',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
