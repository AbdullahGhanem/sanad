<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrisisHelpResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'icon',
        'title_en',
        'title_ar',
        'value',
        'detail_en',
        'detail_ar',
        'url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getTitle(string $language = 'en'): string
    {
        return $language === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getDetail(string $language = 'en'): ?string
    {
        return $language === 'ar' ? $this->detail_ar : $this->detail_en;
    }

    public static function hotlineNumber(): string
    {
        return once(fn () => static::query()
            ->where('type', 'phone')
            ->active()
            ->ordered()
            ->value('value') ?? '');
    }
}
