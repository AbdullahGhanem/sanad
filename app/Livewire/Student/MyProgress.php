<?php

namespace App\Livewire\Student;

use App\Enums\ReferralStatus;
use App\Models\Referral;
use App\Models\ScreeningSession;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Progress')]
class MyProgress extends Component
{
    /**
     * Retroactively link any anonymous screenings taken in this browser session
     * to the now-authenticated student, so their history is complete.
     */
    public function mount(): void
    {
        $guestId = session('guest_id');

        if ($guestId) {
            ScreeningSession::query()
                ->whereNull('user_id')
                ->where('anonymous_id', $guestId)
                ->update(['user_id' => Auth::id()]);
        }
    }

    /**
     * @return Collection<int, ScreeningSession>
     */
    #[Computed]
    public function sessions(): Collection
    {
        return Auth::user()->screeningSessions()
            ->whereNotNull('completed_at')
            ->orderBy('completed_at')
            ->get();
    }

    #[Computed]
    public function isRtl(): bool
    {
        return app()->getLocale() === 'ar';
    }

    /**
     * Whether the student already has an open self-requested counselor referral.
     */
    #[Computed]
    public function hasPendingRequest(): bool
    {
        return Referral::query()
            ->where('user_id', Auth::id())
            ->whereNull('referred_by_id')
            ->open()
            ->exists();
    }

    /**
     * Raise a self-initiated referral so the counseling center reaches out.
     */
    public function requestCounselor(): void
    {
        if ($this->hasPendingRequest) {
            return;
        }

        Referral::create([
            'user_id' => Auth::id(),
            'anonymous_id' => session('guest_id'),
            'referred_by_id' => null,
            'status' => ReferralStatus::Pending,
            'reason' => 'Student-requested counselor support.',
        ]);

        unset($this->hasPendingRequest);
    }

    /**
     * Direction of change between the two most recent PHQ-9 scores
     * (lower is better), or null when there is not enough history.
     */
    #[Computed]
    public function trend(): ?string
    {
        $scores = $this->sessions->pluck('phq9_score')->filter(fn ($v): bool => $v !== null)->values();

        if ($scores->count() < 2) {
            return null;
        }

        $latest = $scores->last();
        $previous = $scores->slice(-2, 1)->first();

        return match (true) {
            $latest < $previous => 'improving',
            $latest > $previous => 'worsening',
            default => 'stable',
        };
    }

    /**
     * SVG polyline points for the PHQ-9 trend sparkline.
     */
    #[Computed]
    public function phq9Sparkline(): string
    {
        return $this->sparkline($this->sessions->pluck('phq9_score'), 27);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int|null>  $scores
     */
    private function sparkline(\Illuminate\Support\Collection $scores, int $max): string
    {
        $scores = $scores->filter(fn ($v): bool => $v !== null)->values();
        $count = $scores->count();

        if ($count < 2) {
            return '';
        }

        $width = 100;
        $height = 32;

        return $scores
            ->map(function (int $score, int $index) use ($count, $width, $height, $max): string {
                $x = round($index / ($count - 1) * $width, 2);
                $y = round($height - ($score / $max) * $height, 2);

                return "{$x},{$y}";
            })
            ->implode(' ');
    }

    public function severityColor(?string $severity): string
    {
        return match ($severity) {
            'minimal' => 'text-green-600 dark:text-green-400',
            'mild' => 'text-yellow-600 dark:text-yellow-400',
            'moderate' => 'text-orange-600 dark:text-orange-400',
            'moderately_severe' => 'text-red-500 dark:text-red-400',
            'severe' => 'text-red-700 dark:text-red-300',
            default => 'text-zinc-600 dark:text-zinc-400',
        };
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.student.my-progress');
    }
}
