<?php

namespace App\Jobs;

use App\Models\KnowledgeArticle;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;

class EmbedKnowledgeArticle implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $knowledgeArticleId,
    ) {}

    /**
     * Generate and persist the English and Arabic embeddings for the article.
     */
    public function handle(): void
    {
        if (blank(config('ai.providers.voyageai.key'))) {
            Log::warning('Skipped knowledge article embedding: VOYAGEAI_API_KEY is not configured.', [
                'knowledge_article_id' => $this->knowledgeArticleId,
            ]);

            return;
        }

        $article = KnowledgeArticle::find($this->knowledgeArticleId);

        if (! $article) {
            return;
        }

        $response = Embeddings::for([
            $article->embeddableText('en'),
            $article->embeddableText('ar'),
        ])->generate(provider: Lab::VoyageAI);

        $article->forceFill([
            'embedding_en' => $response->embeddings[0],
            'embedding_ar' => $response->embeddings[1],
            'embedding_model' => $response->meta->model,
        ])->saveQuietly();
    }
}
