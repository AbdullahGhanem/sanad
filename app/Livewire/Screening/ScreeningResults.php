<?php

namespace App\Livewire\Screening;

use App\Models\Recommendation;
use App\Models\ScreeningSession;
use App\Services\CombinedScoringService;
use App\Services\Gad7ScoringService;
use App\Services\Phq9ScoringService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Screening Results')]
#[Layout('layouts.screening')]
class ScreeningResults extends Component
{
    public ScreeningSession $session;

    public string $language = 'ar';

    public function mount(ScreeningSession $session): void
    {
        $this->session = $session;
        $this->language = app()->getLocale();
    }

    #[Computed]
    public function isRtl(): bool
    {
        return $this->language === 'ar';
    }

    #[Computed]
    public function phq9Severity(): string
    {
        return app(Phq9ScoringService::class)->getSeverity($this->session->phq9_score);
    }

    #[Computed]
    public function gad7Severity(): string
    {
        return app(Gad7ScoringService::class)->getSeverity($this->session->gad7_score);
    }

    #[Computed]
    public function phq9Description(): string
    {
        return app(Phq9ScoringService::class)->getSeverityDescription($this->session->phq9_score, $this->language);
    }

    #[Computed]
    public function gad7Description(): string
    {
        return app(Gad7ScoringService::class)->getSeverityDescription($this->session->gad7_score, $this->language);
    }

    #[Computed]
    public function combinedDescription(): string
    {
        return app(CombinedScoringService::class)->getCombinedDescription(
            $this->session->phq9_score,
            $this->session->gad7_score,
            $this->language,
        );
    }

    #[Computed]
    public function severityColor(): string
    {
        return match ($this->session->combined_severity) {
            'minimal' => 'text-green-600 dark:text-green-400',
            'mild' => 'text-yellow-600 dark:text-yellow-400',
            'moderate' => 'text-orange-600 dark:text-orange-400',
            'moderately_severe' => 'text-red-500 dark:text-red-400',
            'severe' => 'text-red-700 dark:text-red-300',
            default => 'text-zinc-600 dark:text-zinc-400',
        };
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Recommendation>
     */
    #[Computed]
    public function recommendations(): \Illuminate\Database\Eloquent\Collection
    {
        return Recommendation::query()
            ->where('min_phq9', '<=', $this->session->phq9_score)
            ->where('max_phq9', '>=', $this->session->phq9_score)
            ->where('min_gad7', '<=', $this->session->gad7_score)
            ->where('max_gad7', '>=', $this->session->gad7_score)
            ->get();
    }

    public function switchLanguage(string $language): void
    {
        $this->language = in_array($language, ['ar', 'en']) ? $language : 'en';
        app()->setLocale($this->language);
        session()->put('locale', $this->language);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        app()->setLocale($this->language);

        return view('livewire.screening.screening-results');
    }
}
