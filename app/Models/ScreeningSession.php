<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScreeningSession extends Model
{
    /** @use HasFactory<\Database\Factories\ScreeningSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'anonymous_id',
        'phq9_score',
        'gad7_score',
        'combined_severity',
        'nlp_classification',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SessionAnswer::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
