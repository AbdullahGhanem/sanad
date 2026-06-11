<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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

    /**
     * Message content rendered as sanitized HTML; raw HTML in the source is
     * stripped so model output can never inject markup or scripts.
     *
     * @return Attribute<string, never>
     */
    protected function renderedContent(): Attribute
    {
        return Attribute::get(fn (): string => Str::markdown($this->content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    }
}
