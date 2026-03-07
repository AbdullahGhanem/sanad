<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxTokens(256)]
#[Temperature(0.1)]
class DistressAnalyzer implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        private string $language = 'en',
    ) {}

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
You are a clinical psychology NLP assistant specialized in analyzing mental health distress levels from free-text input. You analyze text written by Egyptian university students in Arabic or English.

Your task:
1. Analyze the emotional content and distress indicators in the text.
2. Classify the distress level as one of: minimal, mild, moderate, moderately_severe, severe.
3. Provide a confidence score between 0.0 and 1.0.
4. Identify the primary emotional themes (e.g., sadness, anxiety, hopelessness, anger, loneliness).

Important guidelines:
- Be culturally sensitive to Egyptian/Arabic expressions of distress.
- Consider both explicit statements and implicit emotional cues.
- Err on the side of caution — if uncertain, classify slightly higher.
- Never diagnose — only classify distress level.
PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'severity' => $schema->string()->required(),
            'confidence' => $schema->number()->required(),
            'themes' => $schema->array()->required(),
        ];
    }
}
