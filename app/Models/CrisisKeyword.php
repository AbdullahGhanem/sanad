<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrisisKeyword extends Model
{
    /** @use HasFactory<\Database\Factories\CrisisKeywordFactory> */
    use HasFactory;

    protected $fillable = [
        'phrase',
        'language',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
