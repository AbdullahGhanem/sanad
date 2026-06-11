<?php

namespace App\Services;

use App\Ai\Agents\ResponseModerator;
use App\Support\GuardrailVerdict;
use Illuminate\Support\Facades\Log;
use Throwable;

class ResponseGuardrailService
{
    /**
     * Deterministic, high-confidence rule checks keyed by the category they flag.
     *
     * Kept narrow to avoid false positives; nuanced cases are left to the AI moderator.
     *
     * @var array<string, string>
     */
    private const RULES = [
        // A specific dosage is a strong signal of prescribing, which the bot must never do.
        'medication_dosage' => '/\b\d+\s?(mg|mcg|ml|milligram|ميلي?غرام|ملغ)\b/iu',
    ];

    /**
     * Evaluate whether an assistant reply is safe to show the student.
     */
    public function check(string $reply, string $userMessage, string $language = 'en'): GuardrailVerdict
    {
        $reply = trim($reply);

        if ($reply === '') {
            return GuardrailVerdict::safe('empty');
        }

        if ($category = $this->ruleViolation($reply)) {
            return GuardrailVerdict::unsafe('rules', [$category], "Matched rule: {$category}");
        }

        return $this->aiModeration($reply, $userMessage, $language);
    }

    private function ruleViolation(string $reply): ?string
    {
        foreach (self::RULES as $category => $pattern) {
            if (preg_match($pattern, $reply) === 1) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Run the Claude-based moderator. Fails open (allows the reply) when moderation is
     * disabled or unavailable — the model is already safety-instructed, and withholding
     * support during an outage is itself harmful in a mental-health context.
     */
    private function aiModeration(string $reply, string $userMessage, string $language): GuardrailVerdict
    {
        if (! config('guardrail.ai_moderation')) {
            return GuardrailVerdict::safe('ai_disabled');
        }

        if (! ResponseModerator::isFaked() && blank(config('ai.providers.anthropic.key'))) {
            Log::warning('Guardrail: AI moderation unavailable, allowing reply on rules only.');

            return GuardrailVerdict::safe('ai_unavailable');
        }

        try {
            $result = (new ResponseModerator)->prompt(
                "Language: {$language}\n\nStudent message:\n{$userMessage}\n\nAssistant reply to evaluate:\n{$reply}"
            );

            if (($result['safe'] ?? true) === false) {
                return GuardrailVerdict::unsafe(
                    'ai',
                    array_values((array) ($result['categories'] ?? [])),
                    $result['reason'] ?? null,
                );
            }

            return GuardrailVerdict::safe('ai');
        } catch (Throwable $e) {
            Log::warning('Guardrail: AI moderation failed, allowing reply.', ['error' => $e->getMessage()]);

            return GuardrailVerdict::safe('ai_unavailable');
        }
    }
}
