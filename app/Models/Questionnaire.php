<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Questionnaire extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionnaireFactory> */
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'type',
        'version',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }
}
