<?php

namespace App\Jobs;

use App\Ai\Agents\SanadChat;
use App\Models\ChatMessage;
use App\Models\ScreeningSession;
use App\Services\ContextInjectionService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Laravel\Ai\Responses\Data\Usage;
use Laravel\Ai\Responses\StreamedAgentResponse;
use Laravel\Ai\Streaming\Events\StreamEnd;
use Throwable;

class StreamChatResponse implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $chatSessionId,
        public ?int $screeningSessionId,
        public string $userMessage,
        public string $language,
    ) {}

    /**
     * Generate the assistant reply, streaming each token to the chat channel and
     * persisting the final message once the stream completes.
     */
    public function handle(ContextInjectionService $context): void
    {
        $channel = new Channel($this->channelName());
        $agent = (new SanadChat($this->chatSessionId, $this->language))
            ->forScreeningSession($this->screeningSessionId);

        if ($session = $this->screeningSession()) {
            $agent->withScreeningContext($context->buildContext($session, $this->language));
        }

        try {
            $agent->broadcastNow($this->userMessage, $channel)
                ->then(fn (StreamedAgentResponse $response) => $this->persist((string) $response->text));
        } catch (Throwable $e) {
            report($e);
            $this->persist($this->fallback());
            (new StreamEnd(Str::ulid(), 'error', new Usage, time()))->broadcastNow($channel);
        }
    }

    /**
     * Persist the assistant message.
     */
    private function persist(string $content): void
    {
        ChatMessage::create([
            'session_id' => $this->chatSessionId,
            'screening_session_id' => $this->screeningSessionId,
            'role' => 'assistant',
            'content' => $content,
            'language' => $this->language,
            'detected_crisis' => false,
        ]);
    }

    private function screeningSession(): ?ScreeningSession
    {
        return $this->screeningSessionId
            ? ScreeningSession::find($this->screeningSessionId)
            : null;
    }

    public function channelName(): string
    {
        return 'chat.'.$this->chatSessionId;
    }

    private function fallback(): string
    {
        return $this->language === 'ar'
            ? 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.'
            : 'Sorry, something went wrong. Please try again.';
    }
}
