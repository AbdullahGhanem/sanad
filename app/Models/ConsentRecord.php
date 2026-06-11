<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentRecord extends Model
{
    /** @use HasFactory<\Database\Factories\ConsentRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'anonymous_id',
        'version',
        'locale',
        'ip_address',
        'agreed_at',
    ];

    protected function casts(): array
    {
        return [
            'agreed_at' => 'datetime',
        ];
    }

    /**
     * Whether the given anonymous subject has agreed to the given consent version.
     */
    public static function hasConsented(string $anonymousId, string $version): bool
    {
        if ($anonymousId === '') {
            return false;
        }

        return static::query()
            ->where('anonymous_id', $anonymousId)
            ->where('version', $version)
            ->exists();
    }

    /**
     * Record a subject's agreement to a consent version.
     */
    public static function record(string $anonymousId, string $version, ?string $locale = null, ?string $ipAddress = null): self
    {
        return static::query()->create([
            'anonymous_id' => $anonymousId,
            'version' => $version,
            'locale' => $locale,
            'ip_address' => $ipAddress,
            'agreed_at' => now(),
        ]);
    }
}
