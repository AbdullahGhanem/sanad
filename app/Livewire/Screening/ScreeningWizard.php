<?php

namespace App\Livewire\Screening;

use App\Ai\Agents\DistressAnalyzer;
use App\Models\CrisisHelpResource;
use App\Models\Questionnaire;
use App\Models\ScreeningSession;
use App\Services\CombinedScoringService;
use App\Services\EnsembleScoringService;
use App\Services\Gad7ScoringService;
use App\Services\Phq9ScoringService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mental Health Screening')]
#[Layout('layouts.screening')]
class ScreeningWizard extends Component
{
    public string $language = 'ar';

    public string $phase = 'phq9';

    public int $currentStep = 0;

    /** @var array<int, int|null> */
    public array $answers = [];

    public bool $showCrisisOverlay = false;

    public bool $crisisAcknowledged = false;

    public bool $isCompleted = false;

    public ?int $screeningSessionId = null;

    public string $freeText = '';

    public bool $isAnalyzing = false;

    public function mount(): void
    {
        $this->language = app()->getLocale();

        $saved = session('screening_wizard_state');

        if ($saved && ($saved['language'] ?? null) === $this->language) {
            $this->phase = $saved['phase'];
            $this->currentStep = $saved['currentStep'];
            $this->answers = $saved['answers'];
            $this->crisisAcknowledged = $saved['crisisAcknowledged'] ?? false;
            $this->freeText = $saved['freeText'] ?? '';
        } else {
            $this->initializeAnswers();
        }
    }

    private function saveStateToSession(): void
    {
        session(['screening_wizard_state' => [
            'language' => $this->language,
            'phase' => $this->phase,
            'currentStep' => $this->currentStep,
            'answers' => $this->answers,
            'crisisAcknowledged' => $this->crisisAcknowledged,
            'freeText' => $this->freeText,
        ]]);
    }

    private function initializeAnswers(): void
    {
        $totalQuestions = $this->phq9Questions()->count() + $this->gad7Questions()->count();

        for ($i = 0; $i < $totalQuestions; $i++) {
            $this->answers[$i] = null;
        }
    }

    #[Computed]
    public function phq9Questionnaire(): Questionnaire
    {
        return Questionnaire::where('type', 'PHQ-9')
            ->whereNotNull('published_at')
            ->latest('version')
            ->firstOrFail();
    }

    #[Computed]
    public function gad7Questionnaire(): Questionnaire
    {
        return Questionnaire::where('type', 'GAD-7')
            ->whereNotNull('published_at')
            ->latest('version')
            ->firstOrFail();
    }

    #[Computed]
    public function phq9Questions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->phq9Questionnaire->questions()->with('options')->orderBy('order')->get();
    }

    #[Computed]
    public function gad7Questions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->gad7Questionnaire->questions()->with('options')->orderBy('order')->get();
    }

    #[Computed]
    public function currentQuestion(): ?\App\Models\Question
    {
        if ($this->phase === 'free_text') {
            return null;
        }

        $allQuestions = $this->allQuestions();

        return $allQuestions[$this->currentStep] ?? null;
    }

    #[Computed]
    public function totalSteps(): int
    {
        return $this->phq9Questions()->count() + $this->gad7Questions()->count();
    }

    #[Computed]
    public function progressPercentage(): int
    {
        if ($this->totalSteps === 0) {
            return 0;
        }

        if ($this->phase === 'free_text') {
            return 100;
        }

        return (int) round(($this->currentStep / $this->totalSteps) * 100);
    }

    #[Computed]
    public function isRtl(): bool
    {
        return $this->language === 'ar';
    }

    #[Computed]
    public function crisisHelpResources(): \Illuminate\Database\Eloquent\Collection
    {
        return CrisisHelpResource::active()->ordered()->get();
    }

    /**
     * @return array<int, \App\Models\Question>
     */
    private function allQuestions(): array
    {
        return $this->phq9Questions()->merge($this->gad7Questions())->values()->all();
    }

    public function selectOption(int $value): void
    {
        $this->answers[$this->currentStep] = $value;
        $this->checkCrisisTrigger();

        if (! $this->showCrisisOverlay) {
            $this->next();
        }

        $this->saveStateToSession();
    }

    public function next(): void
    {
        if ($this->phase === 'free_text') {
            return;
        }

        if ($this->answers[$this->currentStep] === null) {
            return;
        }

        if ($this->currentStep + 1 >= $this->totalSteps) {
            $this->phase = 'free_text';

            return;
        }

        $this->currentStep++;
        $this->updatePhase();
    }

    public function previous(): void
    {
        if ($this->phase === 'free_text') {
            $this->phase = $this->currentStep < $this->phq9Questions()->count() ? 'phq9' : 'gad7';
            $this->currentStep = $this->totalSteps - 1;
            $this->updatePhase();
            $this->saveStateToSession();

            return;
        }

        if ($this->currentStep > 0) {
            $this->currentStep--;
            $this->updatePhase();
            $this->saveStateToSession();
        }
    }

    public function skipFreeText(): void
    {
        $this->completeScreening();
    }

    public function submitFreeText(): void
    {
        $this->validate([
            'freeText' => 'required|string|max:500',
        ]);

        $this->completeScreening();
    }

    private function updatePhase(): void
    {
        $phq9Count = $this->phq9Questions()->count();
        $this->phase = $this->currentStep < $phq9Count ? 'phq9' : 'gad7';
    }

    private function checkCrisisTrigger(): void
    {
        $phq9Count = $this->phq9Questions()->count();
        $item9Index = $phq9Count - 1;

        if ($this->currentStep === $item9Index && ! $this->crisisAcknowledged) {
            $item9Score = $this->answers[$item9Index] ?? 0;
            $phq9Service = app(Phq9ScoringService::class);

            if ($phq9Service->isCrisisIndicator($item9Score)) {
                $this->showCrisisOverlay = true;
            }
        }
    }

    public function acknowledgeCrisis(): void
    {
        $this->crisisAcknowledged = true;
        $this->showCrisisOverlay = false;

        $this->logCrisisEvent();
        $this->next();
    }

    private function logCrisisEvent(): void
    {
        \App\Models\CrisisEvent::create([
            'anonymous_id' => session('guest_id', Str::uuid()->toString()),
            'source' => 'screening',
            'severity' => 'high',
        ]);
    }

    private function completeScreening(): void
    {
        $phq9Service = app(Phq9ScoringService::class);
        $gad7Service = app(Gad7ScoringService::class);
        $combinedService = app(CombinedScoringService::class);

        $phq9Count = $this->phq9Questions()->count();

        $phq9Answers = array_slice($this->answers, 0, $phq9Count);
        $gad7Answers = array_slice($this->answers, $phq9Count);

        $phq9Score = $phq9Service->calculateScore(array_map('intval', $phq9Answers));
        $gad7Score = $gad7Service->calculateScore(array_map('intval', $gad7Answers));
        $combinedSeverity = $combinedService->getCombinedSeverity($phq9Score, $gad7Score);

        $nlpClassification = null;

        if (! empty($this->freeText)) {
            $nlpClassification = $this->analyzeFreeTe($this->freeText);

            if ($nlpClassification) {
                $ensembleService = app(EnsembleScoringService::class);
                $combinedSeverity = $ensembleService->mergeConservative(
                    $combinedSeverity,
                    $nlpClassification['severity'],
                    $nlpClassification['confidence'],
                );
            }
        }

        $session = ScreeningSession::create([
            'user_id' => Auth::id(),
            'anonymous_id' => session('guest_id', Str::uuid()->toString()),
            'phq9_score' => $phq9Score,
            'gad7_score' => $gad7Score,
            'combined_severity' => $combinedSeverity,
            'nlp_classification' => $nlpClassification ? $nlpClassification['severity'] : null,
            'completed_at' => now(),
        ]);

        $this->saveAnswersToSession($session);

        if (Auth::check()) {
            Auth::user()->update(['last_screened_at' => now()]);
        }

        $this->screeningSessionId = $session->id;
        $this->isCompleted = true;

        session()->forget('screening_wizard_state');
    }

    /**
     * @return array{severity: string, confidence: float, themes: array<string>}|null
     */
    private function analyzeFreeTe(string $text): ?array
    {
        try {
            $response = (new DistressAnalyzer($this->language))
                ->prompt("Analyze the following text for mental health distress indicators:\n\n{$text}");

            return [
                'severity' => $response['severity'] ?? 'minimal',
                'confidence' => (float) ($response['confidence'] ?? 0.5),
                'themes' => $response['themes'] ?? [],
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function saveAnswersToSession(ScreeningSession $session): void
    {
        $allQuestions = $this->allQuestions();

        foreach ($this->answers as $index => $value) {
            if ($value === null || ! isset($allQuestions[$index])) {
                continue;
            }

            $question = $allQuestions[$index];
            $option = $question->options()->where('value', $value)->first();

            $session->answers()->create([
                'question_id' => $question->id,
                'question_option_id' => $option?->id,
            ]);
        }

        if (! empty($this->freeText)) {
            $session->answers()->create([
                'question_id' => null,
                'question_option_id' => null,
                'free_text' => $this->freeText,
            ]);
        }
    }

    public function switchLanguage(string $language): void
    {
        $this->language = in_array($language, ['ar', 'en']) ? $language : 'en';
        app()->setLocale($this->language);
        session()->put('locale', $this->language);
        $this->saveStateToSession();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        app()->setLocale($this->language);

        return view('livewire.screening.screening-wizard');
    }
}
