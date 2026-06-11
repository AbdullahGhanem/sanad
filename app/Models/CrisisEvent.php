<?php

namespace App\Models;

use App\Enums\CrisisStatus;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrisisEvent extends Model
{
    /** @use HasFactory<\Database\Factories\CrisisEventFactory> */
    use Auditable, HasFactory;

    protected string $auditLogName = 'crisis';

    protected $fillable = [
        'anonymous_id',
        'source',
        'severity',
        'status',
        'handled_by_id',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CrisisStatus::class,
            'handled_at' => 'datetime',
        ];
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CounselorNote::class)->latest();
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function hasReferral(): bool
    {
        return $this->referrals()->exists();
    }

    /**
     * @param  Builder<CrisisEvent>  $query
     */
    public function scopeUnresolved(Builder $query): void
    {
        $query->where('status', '!=', CrisisStatus::Resolved->value);
    }

    public function acknowledge(User $counselor): void
    {
        $this->forceFill([
            'status' => CrisisStatus::Acknowledged,
            'handled_by_id' => $counselor->id,
            'handled_at' => now(),
        ])->save();
    }

    public function resolve(User $counselor): void
    {
        $this->forceFill([
            'status' => CrisisStatus::Resolved,
            'handled_by_id' => $counselor->id,
            'handled_at' => now(),
        ])->save();
    }

    public function isResolved(): bool
    {
        return $this->status === CrisisStatus::Resolved;
    }
}
