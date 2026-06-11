<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrisisKeyword extends Model
{
    /** @use HasFactory<\Database\Factories\CrisisKeywordFactory> */
    use Auditable, HasFactory;

    protected string $auditLogName = 'crisis-config';

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
