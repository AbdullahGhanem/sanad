<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ChatMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'screening_session_id',
        'session_id',
        'role',
        'content',
        'language',
        'detected_crisis',
    ];

    protected function casts(): array
    {
        return [
            'detected_crisis' => 'boolean',
        ];
    }

    public function screeningSession(): BelongsTo
    {
        return $this->belongsTo(ScreeningSession::class);
    }
}
