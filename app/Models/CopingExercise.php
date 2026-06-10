<?php

namespace App\Models;

use App\Enums\CopingTheme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CopingExercise extends Model
{
    /** @use HasFactory<\Database\Factories\CopingExerciseFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'themes',
        'title_en',
        'title_ar',
        'summary_en',
        'summary_ar',
        'steps_en',
        'steps_ar',
        'duration_minutes',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'themes' => 'array',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<CopingExercise>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * @param  Builder<CopingExercise>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order');
    }

    /**
     * Scope to exercises that address the given emotional theme.
     *
     * @param  Builder<CopingExercise>  $query
     */
    public function scopeForTheme(Builder $query, string $theme): void
    {
        $query->whereJsonContains('themes', $theme);
    }

    public function getTitle(string $language = 'en'): string
    {
        return $language === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getSummary(string $language = 'en'): ?string
    {
        return $language === 'ar' ? $this->summary_ar : $this->summary_en;
    }

    public function getSteps(string $language = 'en'): string
    {
        return $language === 'ar' ? $this->steps_ar : $this->steps_en;
    }

    public function addressesTheme(CopingTheme $theme): bool
    {
        return in_array($theme->value, $this->themes ?? [], true);
    }
}
