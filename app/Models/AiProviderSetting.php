<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiProviderSetting extends Model
{
    /** @use HasFactory<\Database\Factories\AiProviderSettingFactory> */
    use Auditable, HasFactory;

    protected string $auditLogName = 'ai-config';

    /**
     * Never log the encrypted api_key.
     *
     * @var list<string>
     */
    protected array $auditLogOnly = ['provider', 'model', 'is_active'];

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
