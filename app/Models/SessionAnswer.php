<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\SessionAnswerFactory> */
    use HasFactory;

    protected $fillable = [
        'screening_session_id',
        'question_id',
        'question_option_id',
        'free_text',
    ];

    public function screeningSession(): BelongsTo
    {
        return $this->belongsTo(ScreeningSession::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
