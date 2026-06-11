<?php

namespace Tests\Feature\KnowledgeBase;

use App\Jobs\EmbedKnowledgeArticle;
use App\Models\KnowledgeArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Ai\Embeddings;
use Tests\TestCase;

class EmbedKnowledgeArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_observer_dispatches_embedding_job_on_create(): void
    {
        Queue::fake();

        $article = KnowledgeArticle::factory()->create();

        Queue::assertPushed(
            EmbedKnowledgeArticle::class,
            fn (EmbedKnowledgeArticle $job): bool => $job->knowledgeArticleId === $article->id,
        );
    }

    public function test_observer_does_not_redispatch_when_only_toggling_active(): void
    {
        $article = KnowledgeArticle::factory()->withEmbedding([0.1, 0.2, 0.3])->create();

        Queue::fake();
        $article->update(['is_active' => false]);

        Queue::assertNothingPushed();
    }

    public function test_observer_redispatches_when_body_changes(): void
    {
        $article = KnowledgeArticle::factory()->withEmbedding([0.1, 0.2, 0.3])->create();

        Queue::fake();
        $article->update(['body_en' => 'Substantially rewritten content.']);

        Queue::assertPushed(EmbedKnowledgeArticle::class);
    }

    public function test_job_generates_and_persists_embeddings_when_key_is_present(): void
    {
        $article = KnowledgeArticle::factory()->create();

        config(['ai.providers.voyageai.key' => 'test-key']);
        Embeddings::fake(fn () => [[0.11, 0.22], [0.33, 0.44]]);

        (new EmbedKnowledgeArticle($article->id))->handle();

        $fresh = $article->fresh();
        $this->assertSame([0.11, 0.22], $fresh->embedding_en);
        $this->assertSame([0.33, 0.44], $fresh->embedding_ar);
    }

    public function test_job_no_ops_when_key_is_missing(): void
    {
        config(['ai.providers.voyageai.key' => null]);
        $article = KnowledgeArticle::factory()->create();

        (new EmbedKnowledgeArticle($article->id))->handle();

        $this->assertNull($article->fresh()->embedding_en);
    }
}
