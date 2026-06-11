<?php

namespace App\Livewire\Chat;

use App\Jobs\StreamChatResponse;
use App\Models\ChatMessage;
use App\Models\CrisisHelpResource;
use App\Models\ScreeningSession;
use App\Services\CrisisDetectionService;
use App\Services\TranscriptionService;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Chat with Sanad')]
#[Layout('layouts.screening')]
class ChatInterface extends Component
{
    use WithFileUploads;

    public string $language = 'ar';

    public string $message = '';

    public string $chatSessionId = '';

    public ?int $screeningSessionId = null;

    public bool $isStreaming = false;

    public bool $showCrisisOverlay = false;

    public $audioFile = null;

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

    #[Computed]
    public function streamChannel(): string
    {
        return 'chat.'.$this->chatSessionId;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = trim($this->message);
        $this->message = '';

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
                'chat',
                $this->language,
            );
            $this->showCrisisOverlay = true;
        }

        // Stream the AI response asynchronously over the chat's websocket channel.
        $this->isStreaming = true;

        StreamChatResponse::dispatch(
            $this->chatSessionId,
            $this->screeningSessionId,
            $userMessage,
            $this->language,
        );
    }

    public function finishStreaming(): void
    {
        $this->isStreaming = false;
        unset($this->chatMessages);
    }

    /**
     * Transcribe a recorded voice clip and place the text in the input for review.
     */
    public function updatedAudioFile(): void
    {
        $this->validate([
            'audioFile' => 'file|mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/mp4,audio/wav,audio/x-wav,video/webm|max:25600',
        ]);

        $transcript = app(TranscriptionService::class)->transcribe($this->audioFile, $this->language);

        $this->audioFile = null;

        if ($transcript !== null) {
            $this->message = $transcript;
            $this->dispatch('transcription-ready', text: $transcript);

            return;
        }

        $this->dispatch('transcription-failed');
    }

    public function acknowledgeCrisis(): void
    {
        $this->showCrisisOverlay = false;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.chat.chat-interface');
    }
}
