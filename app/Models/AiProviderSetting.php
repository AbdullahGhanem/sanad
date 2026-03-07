<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiProviderSetting extends Model
{
    /** @use HasFactory<\Database\Factories\AiProviderSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'provider',
        'model',
        'api_key',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public static function active(): ?self
    {
        return static::query()->where('is_active', true)->first();
    }
}
