<?php

namespace App\Ai\Agents;

use App\Ai\Tools\GetCrisisResources;
use App\Ai\Tools\GetMyScreeningResult;
use App\Ai\Tools\SuggestCopingExercise;
use App\Models\ChatMessage;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxSteps(5)]
#[MaxTokens(500)]
#[Temperature(0.7)]
class SanadChat implements Agent, Conversational, HasTools
{
    use Promptable;

    private string $screeningContext = '';

    private ?int $screeningSessionId = null;

    public function __construct(
        private string $sessionId = '',
        private string $language = 'en',
    ) {}

    public function withScreeningContext(string $context): self
    {
        $this->screeningContext = $context;

        return $this;
    }

    public function forScreeningSession(?int $screeningSessionId): self
    {
        $this->screeningSessionId = $screeningSessionId;

        return $this;
    }

    public function instructions(): Stringable|string
    {
        $languageInstruction = $this->language === 'ar'
            ? 'You MUST respond entirely in Arabic (Egyptian dialect preferred). Never respond in English. The student communicates in Arabic.'
            : 'Respond in English. The student prefers English communication.';

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
- If the student expresses suicidal thoughts, self-harm, or acute distress, you MUST call the get_crisis_resources tool to retrieve current crisis support details, then share them with the student. Never invent or recall hotline numbers from memory.
- If the student asks about their screening results, scores, or which specific symptoms they reported, call the get_my_screening_result tool to retrieve the detailed breakdown. Never invent scores or answers.
- When offering a practical coping strategy, call the suggest_coping_exercise tool with the relevant emotional theme and share the returned steps. Do not invent your own exercises.
- Keep responses concise (2-4 paragraphs max).
- Ask follow-up questions to understand the student's situation better.{$contextBlock}
PROMPT;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return \Laravel\Ai\Contracts\Tool[]
     */
    public function tools(): iterable
    {
        return [
            new GetCrisisResources($this->language),
            new GetMyScreeningResult($this->screeningSessionId, $this->language),
            new SuggestCopingExercise($this->language),
        ];
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
