<?php

namespace App\Livewire\Chat;

use App\Ai\Agents\SanadChat;
use App\Models\ChatMessage;
use App\Models\CrisisHelpResource;
use App\Models\ScreeningSession;
use App\Services\ContextInjectionService;
use App\Services\CrisisDetectionService;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Chat with Sanad')]
#[Layout('layouts.screening')]
class ChatInterface extends Component
{
    public string $language = 'ar';

    public string $message = '';

    public string $chatSessionId = '';

    public ?int $screeningSessionId = null;

    public bool $isStreaming = false;

    public bool $showCrisisOverlay = false;

    public string $streamedResponse = '';

    public function mount(?int $session = null): void
    {
        $this->language = app()->getLocale();
        $this->chatSessionId = session('chat_session_id', Str::uuid()->toString());
        session()->put('chat_session_id', $this->chatSessionId);
        $this->screeningSessionId = $session;
    }

    #[Computed]
    public function isRtl(): bool
    {
        return $this->language === 'ar';
    }

    #[Computed]
    public function chatMessages(): \Illuminate\Database\Eloquent\Collection
    {
        return ChatMessage::where('session_id', $this->chatSessionId)
            ->orderBy('created_at')
            ->get();
    }

    #[Computed]
    public function crisisHelpResources(): \Illuminate\Database\Eloquent\Collection
    {
        return CrisisHelpResource::active()->ordered()->get();
    }

    #[Computed]
    public function screeningSession(): ?ScreeningSession
    {
        if (! $this->screeningSessionId) {
            return null;
        }

        return ScreeningSession::find($this->screeningSessionId);
    }

    public function sendMessage(): void
    {
        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = trim($this->message);
        $this->message = '';

        // Detect language from first message if no messages yet
        if ($this->chatMessages->isEmpty()) {
            $this->detectLanguage($userMessage);
        }

        // Check for crisis keywords
        $crisisService = app(CrisisDetectionService::class);
        $isCrisis = $crisisService->detectCrisisAnyLanguage($userMessage);

        // Save user message
        ChatMessage::create([
            'session_id' => $this->chatSessionId,
            'screening_session_id' => $this->screeningSessionId,
            'role' => 'user',
            'content' => $userMessage,
            'language' => $this->language,
            'detected_crisis' => $isCrisis,
        ]);

        if ($isCrisis) {
            $crisisService->logCrisisEvent(
                session('guest_id', Str::uuid()->toString()),
            );
            $this->showCrisisOverlay = true;
        }

        // Generate AI response
        $this->generateResponse($userMessage);
    }

    private function generateResponse(string $userMessage): void
    {
        try {
            $agent = new SanadChat($this->chatSessionId, $this->language);

            // Inject screening context if available (US-08)
            if ($this->screeningSession) {
                $contextService = app(ContextInjectionService::class);
                $context = $contextService->buildContext($this->screeningSession, $this->language);
                $agent->withScreeningContext($context);
            }

            $response = $agent->prompt($userMessage);

            ChatMessage::create([
                'session_id' => $this->chatSessionId,
                'screening_session_id' => $this->screeningSessionId,
                'role' => 'assistant',
                'content' => (string) $response,
                'language' => $this->language,
                'detected_crisis' => false,
            ]);
        } catch (\Throwable $e) {
            $fallback = $this->language === 'ar'
                ? 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.'
                : 'Sorry, something went wrong. Please try again.';

            ChatMessage::create([
                'session_id' => $this->chatSessionId,
                'screening_session_id' => $this->screeningSessionId,
                'role' => 'assistant',
                'content' => $fallback,
                'language' => $this->language,
                'detected_crisis' => false,
            ]);
        }
    }

    private function detectLanguage(string $text): void
    {
        $hasArabic = preg_match('/[\x{0600}-\x{06FF}]/u', $text);
        $this->language = $hasArabic ? 'ar' : 'en';
        app()->setLocale($this->language);
        session()->put('locale', $this->language);
    }

    public function acknowledgeCrisis(): void
    {
        $this->showCrisisOverlay = false;
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

        return view('livewire.chat.chat-interface');
    }
}
