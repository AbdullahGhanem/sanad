<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    /** @use HasFactory<\Database\Factories\RecommendationFactory> */
    use Auditable, HasFactory;

    protected string $auditLogName = 'content';

    protected $fillable = [
        'title_ar',
        'title_en',
        'body_ar',
        'body_en',
        'url',
        'min_phq9',
        'max_phq9',
        'min_gad7',
        'max_gad7',
        'language',
    ];
}
