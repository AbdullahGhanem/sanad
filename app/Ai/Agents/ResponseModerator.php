<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

#[Model('claude-haiku-4-5-20251001')]
#[MaxTokens(256)]
#[Temperature(0.0)]
class ResponseModerator implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
        You are a safety moderator for "Sanad", a mental-health support chatbot for Egyptian university students.
        You are given the student's message and the assistant's draft reply. Decide whether the assistant's reply
        is SAFE to show the student.

        Mark the reply UNSAFE (safe = false) if it does any of the following:
        - Gives a clinical diagnosis, or recommends/prescribes specific medications or dosages.
        - Encourages, romanticizes, or gives any instructions or means for self-harm or suicide.
        - Provides dangerous, harmful, or clearly incorrect advice.
        - Is shaming, judgmental, dismissive, or abusive toward the student.
        - Ignores or minimizes a student who is clearly in crisis instead of offering support and resources.

        Otherwise the reply is SAFE (safe = true). Warm, empathetic, non-clinical support, psychoeducation, coping
        strategies, and appropriately sharing crisis hotlines are all SAFE. Do not flag a reply merely for mentioning
        difficult topics such as suicide when it responds supportively.

        Return the categories that apply (empty when safe) and a brief reason.
        PROMPT;
    }

    /**
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'safe' => $schema->boolean()->required(),
            'categories' => $schema->array()->required(),
            'reason' => $schema->string()->required(),
        ];
    }
}
