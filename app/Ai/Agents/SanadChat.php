<?php

namespace App\Ai\Agents;

use App\Models\ChatMessage;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxTokens(500)]
#[Temperature(0.7)]
class SanadChat implements Agent, Conversational
{
    use Promptable;

    private string $screeningContext = '';

    public function __construct(
        private string $sessionId = '',
        private string $language = 'en',
    ) {}

    public function withScreeningContext(string $context): self
    {
        $this->screeningContext = $context;

        return $this;
    }

    public function instructions(): Stringable|string
    {
        $languageInstruction = $this->language === 'ar'
            ? 'Respond in Arabic. The student prefers Arabic communication.'
            : 'Respond in English. The student prefers English communication.';

        $hotline = \App\Models\CrisisHelpResource::hotlineNumber();

        $contextBlock = $this->screeningContext
            ? "\n\nScreening Context:\n{$this->screeningContext}\nUse this context to personalize your responses. Do not repeat questionnaire questions. Reference the student's situation naturally."
            : '';

        return <<<PROMPT
You are Sanad, a compassionate and supportive mental health chatbot designed for Egyptian university students. You provide emotional support, psychoeducation, and coping strategies.

{$languageInstruction}

Core guidelines:
- Be warm, empathetic, and non-judgmental.
- Use simple, clear language appropriate for university students.
- Never diagnose or prescribe medication.
- Encourage professional help when appropriate.
- Be culturally sensitive to Egyptian culture and values.
- If the student expresses suicidal thoughts or self-harm, immediately provide crisis resources (Egypt Mental Health Hotline: {$hotline}, Nefsy.com).
- Keep responses concise (2-4 paragraphs max).
- Ask follow-up questions to understand the student's situation better.{$contextBlock}
PROMPT;
    }

    public function messages(): iterable
    {
        if (! $this->sessionId) {
            return [];
        }

        return ChatMessage::where('session_id', $this->sessionId)
            ->orderBy('created_at')
            ->limit(20)
            ->get()
            ->map(fn (ChatMessage $message) => new Message($message->role, $message->content))
            ->all();
    }
}
