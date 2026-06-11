<?php

namespace App\Models;

use App\Enums\ReferralStatus;
use App\Models\Concerns\Auditable;
use App\Observers\ReferralObserver;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(ReferralObserver::class)]
class Referral extends Model
{
    /** @use HasFactory<\Database\Factories\ReferralFactory> */
    use Auditable, HasFactory;

    protected string $auditLogName = 'referral';

    protected $fillable = [
        'crisis_event_id',
        'user_id',
        'anonymous_id',
        'referred_by_id',
        'status',
        'reason',
        'scheduled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReferralStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }

    public function crisisEvent(): BelongsTo
    {
        return $this->belongsTo(CrisisEvent::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    /**
     * The student the referral is for (set when self-requested).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isSelfRequested(): bool
    {
        return $this->referred_by_id === null && $this->user_id !== null;
    }

    /**
     * @param  Builder<Referral>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->whereIn('status', [ReferralStatus::Pending->value, ReferralStatus::Scheduled->value]);
    }

    public function schedule(CarbonInterface $at): void
    {
        $this->update(['status' => ReferralStatus::Scheduled, 'scheduled_at' => $at]);
    }

    public function complete(): void
    {
        $this->update(['status' => ReferralStatus::Completed]);
    }

    public function decline(): void
    {
        $this->update(['status' => ReferralStatus::Declined]);
    }
}
