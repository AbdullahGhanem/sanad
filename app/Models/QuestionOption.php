<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionOptionFactory> */
    use HasFactory;

    protected $fillable = [
        'question_id',
        'label_ar',
        'label_en',
        'value',
        'order',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
