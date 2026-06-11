<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Observers\KnowledgeArticleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(KnowledgeArticleObserver::class)]
class KnowledgeArticle extends Model
{
    /** @use HasFactory<\Database\Factories\KnowledgeArticleFactory> */
    use Auditable, HasFactory;

    protected string $auditLogName = 'content';

    /**
     * Whitelist content fields only — never log the embedding vectors.
     *
     * @var list<string>
     */
    protected array $auditLogOnly = ['category', 'title_en', 'title_ar', 'body_en', 'body_ar', 'is_active'];

    protected $fillable = [
        'category',
        'title_en',
        'title_ar',
        'body_en',
        'body_ar',
        'embedding_en',
        'embedding_ar',
        'embedding_model',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'embedding_en' => 'array',
            'embedding_ar' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<KnowledgeArticle>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope to articles that have an embedding for the given language.
     *
     * @param  Builder<KnowledgeArticle>  $query
     */
    public function scopeEmbeddedFor(Builder $query, string $language): void
    {
        $query->whereNotNull($this->embeddingColumn($language));
    }

    public function getTitle(string $language = 'en'): string
    {
        return $language === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getBody(string $language = 'en'): string
    {
        return $language === 'ar' ? $this->body_ar : $this->body_en;
    }

    /**
     * The text that should be embedded for the given language.
     */
    public function embeddableText(string $language): string
    {
        return $this->getTitle($language)."\n".$this->getBody($language);
    }

    /**
     * The stored embedding vector for the given language, if any.
     *
     * @return list<float>|null
     */
    public function embeddingFor(string $language): ?array
    {
        return $language === 'ar' ? $this->embedding_ar : $this->embedding_en;
    }

    public function embeddingColumn(string $language): string
    {
        return $language === 'ar' ? 'embedding_ar' : 'embedding_en';
    }
}
