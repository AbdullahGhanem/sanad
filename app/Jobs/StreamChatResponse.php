<?php

namespace App\Jobs;

use App\Ai\Agents\SanadChat;
use App\Models\ChatMessage;
use App\Models\ScreeningSession;
use App\Services\ContextInjectionService;
use App\Services\ResponseGuardrailService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
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
     * Generate the assistant reply, run it through the output guardrail, then stream the
     * approved text to the chat channel and persist it. Nothing is shown to the student
     * until the guardrail has cleared the full reply (moderate-first).
     */
    public function handle(ContextInjectionService $context, ResponseGuardrailService $guardrail): void
    {
        $channel = new Channel($this->channelName());
        $agent = (new SanadChat($this->chatSessionId, $this->language))
            ->forScreeningSession($this->screeningSessionId);

        if ($session = $this->screeningSession()) {
            $agent->withScreeningContext($context->buildContext($session, $this->language));
        }

        try {
            $reply = (string) $agent->prompt($this->userMessage);

            $verdict = $guardrail->check($reply, $this->userMessage, $this->language);

            if ($verdict->blocked()) {
                $logContext = [
                    'session_id' => $this->chatSessionId,
                    'source' => $verdict->source,
                    'categories' => $verdict->categories,
                    'reason' => $verdict->reason,
                ];

                Log::channel('single')->warning('Guardrail blocked an AI reply.', $logContext);
                activity('guardrail')->withProperties($logContext)->log('Guardrail blocked an AI reply');
            }

            $text = $verdict->safe ? $reply : $this->safeFallback();
        } catch (Throwable $e) {
            report($e);
            $text = $this->errorFallback();
        }

        // Persist first so the reply survives even when websockets are unavailable;
        // the client reloads from the database once the stream ends.
        $this->persist($text);

        $this->broadcastApproved($channel, $text);
    }

    /**
     * Stream the approved text to the browser as token deltas, then signal completion.
     *
     * Best-effort: if broadcasting fails (e.g. Reverb is down), the message is already
     * persisted, so the chat recovers via the client-side completion fallback.
     */
    private function broadcastApproved(Channel $channel, string $text): void
    {
        try {
            foreach ($this->chunks($text) as $chunk) {
                Broadcast::on($channel)->as('text_delta')->with(['delta' => $chunk])->sendNow();
            }

            Broadcast::on($channel)->as('stream_end')->with(['reason' => 'stop'])->sendNow();
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Split text into word/whitespace tokens so the reveal preserves formatting.
     *
     * @return list<string>
     */
    private function chunks(string $text): array
    {
        $parts = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        return $parts === false || $parts === [] ? [$text] : $parts;
    }

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

    /**
     * Supportive redirect shown when the guardrail blocks a reply.
     */
    private function safeFallback(): string
    {
        return $this->language === 'ar'
            ? 'أريد أن أتأكد من أن ما أشاركه معك آمن ومفيد. دعنا نركّز على ما تشعر به الآن. وإذا كنت في خطر، فيرجى التواصل مع مختص أو خط دعم نفسي فورًا.'
            : "I want to make sure what I share with you is safe and helpful. Let's focus on how you're feeling right now — and if you're in any danger, please reach out to a professional or a crisis hotline right away.";
    }

    private function errorFallback(): string
    {
        return $this->language === 'ar'
            ? 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.'
            : 'Sorry, something went wrong. Please try again.';
    }
}
