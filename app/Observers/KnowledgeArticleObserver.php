<?php

namespace App\Observers;

use App\Jobs\EmbedKnowledgeArticle;
use App\Models\KnowledgeArticle;

class KnowledgeArticleObserver
{
    /**
     * Columns whose change invalidates the stored embeddings.
     *
     * @var list<string>
     */
    private const EMBEDDABLE_COLUMNS = ['title_en', 'title_ar', 'body_en', 'body_ar'];

    /**
     * Handle the KnowledgeArticle "saved" event.
     */
    public function saved(KnowledgeArticle $knowledgeArticle): void
    {
        $contentChanged = $knowledgeArticle->wasChanged(self::EMBEDDABLE_COLUMNS);
        $missingEmbedding = $knowledgeArticle->embedding_en === null;

        if ($contentChanged || $missingEmbedding) {
            EmbedKnowledgeArticle::dispatch($knowledgeArticle->id);
        }
    }
}
