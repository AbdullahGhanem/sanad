<?php

namespace App\Ai\Tools;

use App\Models\KnowledgeArticle;
use App\Support\VectorMath;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Tools\Request;
use Stringable;
use Throwable;

class SearchPsychoeducation implements Tool
{
    private const LIMIT = 3;

    private const MIN_SIMILARITY = 0.4;

    public function __construct(
        private string $language = 'en',
    ) {}

    /**
     * Get the tool's name as exposed to the model.
     */
    public function name(): string
    {
        return 'search_psychoeducation';
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search the vetted, counselor-approved psychoeducation knowledge base for material '
            .'relevant to the student. Call this before sharing factual or educational mental-health '
            .'information so your answer is grounded in approved content rather than your own recall.';
    }

    /**
     * Get the tool's schema definition.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->required()->description(
                'A short description of the topic or concern to look up (e.g. "sleep problems", '
                .'"panic attacks", "exam stress").'
            ),
        ];
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $query = trim((string) ($request['query'] ?? ''));

        if ($query === '') {
            return $this->noMatchMessage();
        }

        if (! Embeddings::isFaked() && blank(config('ai.providers.voyageai.key'))) {
            return $this->unavailableMessage();
        }

        try {
            $queryVector = Str::of($query)->toEmbeddings(provider: Lab::VoyageAI, cache: true);
        } catch (Throwable $e) {
            Log::warning('Psychoeducation search failed to embed query.', ['exception' => $e->getMessage()]);

            return $this->unavailableMessage();
        }

        $matches = KnowledgeArticle::query()
            ->active()
            ->embeddedFor($this->language)
            ->get()
            ->map(fn (KnowledgeArticle $article): array => [
                'article' => $article,
                'score' => VectorMath::cosineSimilarity($queryVector, $article->embeddingFor($this->language) ?? []),
            ])
            ->filter(fn (array $match): bool => $match['score'] >= self::MIN_SIMILARITY)
            ->sortByDesc('score')
            ->take(self::LIMIT);

        if ($matches->isEmpty()) {
            return $this->noMatchMessage();
        }

        return $matches
            ->map(fn (array $match): string => $this->formatArticle($match['article']))
            ->implode("\n\n");
    }

    /**
     * Format a matched article for the model to draw on.
     */
    private function formatArticle(KnowledgeArticle $article): string
    {
        return "**{$article->getTitle($this->language)}**\n{$article->getBody($this->language)}";
    }

    /**
     * Message used when nothing relevant is found.
     */
    private function noMatchMessage(): string
    {
        return $this->language === 'ar'
            ? 'لا توجد مادة تثقيفية مطابقة في قاعدة المعرفة. قدّم دعمًا عامًا متعاطفًا.'
            : 'No matching material was found in the knowledge base. Offer general, empathetic support.';
    }

    /**
     * Message used when the embedding service is unavailable.
     */
    private function unavailableMessage(): string
    {
        return $this->language === 'ar'
            ? 'قاعدة المعرفة غير متاحة حاليًا. قدّم دعمًا عامًا متعاطفًا.'
            : 'The knowledge base is currently unavailable. Offer general, empathetic support.';
    }
}
