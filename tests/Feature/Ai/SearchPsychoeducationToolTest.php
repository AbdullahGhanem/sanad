<?php

namespace Tests\Feature\Ai;

use App\Ai\Agents\SanadChat;
use App\Ai\Tools\SearchPsychoeducation;
use App\Models\KnowledgeArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class SearchPsychoeducationToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_is_exposed_with_snake_case_name(): void
    {
        $this->assertSame('search_psychoeducation', (new SearchPsychoeducation)->name());
    }

    public function test_schema_requires_a_query(): void
    {
        $schema = (new SearchPsychoeducation)->schema(new JsonSchemaTypeFactory);

        $this->assertArrayHasKey('query', $schema);
    }

    public function test_sanad_chat_registers_the_search_tool(): void
    {
        $tools = iterator_to_array((new SanadChat)->tools());

        $this->assertNotEmpty(array_filter(
            $tools,
            fn ($tool) => $tool instanceof SearchPsychoeducation,
        ));
    }

    public function test_handle_returns_the_most_similar_article(): void
    {
        Embeddings::fake(fn () => [[0.9, 0.1, 0.0]]);
        KnowledgeArticle::factory()->withEmbedding([1.0, 0.0, 0.0])->create([
            'title_en' => 'Managing Anxiety',
            'body_en' => 'Anxiety guidance here.',
        ]);
        KnowledgeArticle::factory()->withEmbedding([0.0, 1.0, 0.0])->create([
            'title_en' => 'Sleep Hygiene',
            'body_en' => 'Sleep guidance here.',
        ]);

        $output = (string) (new SearchPsychoeducation('en'))->handle(new Request(['query' => 'panic attacks']));

        $this->assertStringContainsString('Managing Anxiety', $output);
        $this->assertStringContainsString('Anxiety guidance here.', $output);
        $this->assertStringNotContainsString('Sleep Hygiene', $output);
    }

    public function test_handle_returns_no_match_message_below_threshold(): void
    {
        Embeddings::fake(fn () => [[0.0, 0.0, 1.0]]);
        KnowledgeArticle::factory()->withEmbedding([1.0, 0.0, 0.0])->create();

        $output = (string) (new SearchPsychoeducation('en'))->handle(new Request(['query' => 'unrelated topic']));

        $this->assertStringContainsString('No matching material', $output);
    }

    public function test_handle_excludes_inactive_articles(): void
    {
        Embeddings::fake(fn () => [[1.0, 0.0, 0.0]]);
        KnowledgeArticle::factory()->withEmbedding([1.0, 0.0, 0.0])->inactive()->create([
            'title_en' => 'Hidden Article',
        ]);

        $output = (string) (new SearchPsychoeducation('en'))->handle(new Request(['query' => 'anything']));

        $this->assertStringContainsString('No matching material', $output);
        $this->assertStringNotContainsString('Hidden Article', $output);
    }

    public function test_handle_ranks_against_the_language_specific_embedding(): void
    {
        Embeddings::fake(fn () => [[0.0, 0.0, 1.0]]);
        KnowledgeArticle::factory()->withEmbedding([1.0, 0.0, 0.0], [0.0, 0.0, 1.0])->create([
            'title_ar' => 'القلق',
            'body_ar' => 'محتوى عن القلق',
        ]);

        $output = (string) (new SearchPsychoeducation('ar'))->handle(new Request(['query' => 'قلق']));

        $this->assertStringContainsString('القلق', $output);
        $this->assertStringContainsString('محتوى عن القلق', $output);
    }

    public function test_handle_returns_unavailable_message_without_key_or_fake(): void
    {
        config(['ai.providers.voyageai.key' => null]);
        KnowledgeArticle::factory()->withEmbedding([1.0, 0.0, 0.0])->create();

        $output = (string) (new SearchPsychoeducation('en'))->handle(new Request(['query' => 'anything']));

        $this->assertStringContainsString('knowledge base is currently unavailable', $output);
    }
}
