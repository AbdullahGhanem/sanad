<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselorNote extends Model
{
    /** @use HasFactory<\Database\Factories\CounselorNoteFactory> */
    use HasFactory;

    protected $fillable = [
        'crisis_event_id',
        'user_id',
        'body',
    ];

    public function crisisEvent(): BelongsTo
    {
        return $this->belongsTo(CrisisEvent::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
