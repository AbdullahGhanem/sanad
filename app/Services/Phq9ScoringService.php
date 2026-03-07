<?php

namespace App\Services;

class Phq9ScoringService
{
    /**
     * PHQ-9 severity thresholds.
     *
     * @var array<string, array{min: int, max: int}>
     */
    private const THRESHOLDS = [
        'minimal' => ['min' => 0, 'max' => 4],
        'mild' => ['min' => 5, 'max' => 9],
        'moderate' => ['min' => 10, 'max' => 14],
        'moderately_severe' => ['min' => 15, 'max' => 19],
        'severe' => ['min' => 20, 'max' => 27],
    ];

    /**
     * Calculate the total PHQ-9 score from answer values.
     *
     * @param  array<int>  $answerValues
     */
    public function calculateScore(array $answerValues): int
    {
        return min(array_sum($answerValues), 27);
    }

    /**
     * Get the severity label for a given PHQ-9 score.
     */
    public function getSeverity(int $score): string
    {
        foreach (self::THRESHOLDS as $label => $range) {
            if ($score >= $range['min'] && $score <= $range['max']) {
                return $label;
            }
        }

        return 'severe';
    }

    /**
     * Get a human-readable severity description.
     */
    public function getSeverityDescription(int $score, string $language = 'en'): string
    {
        $severity = $this->getSeverity($score);

        $descriptions = [
            'en' => [
                'minimal' => 'Your responses suggest minimal depression symptoms.',
                'mild' => 'Your responses suggest mild depression symptoms.',
                'moderate' => 'Your responses suggest moderate depression symptoms.',
                'moderately_severe' => 'Your responses suggest moderately severe depression symptoms.',
                'severe' => 'Your responses suggest severe depression symptoms.',
            ],
            'ar' => [
                'minimal' => 'تشير إجاباتك إلى أعراض اكتئاب بسيطة.',
                'mild' => 'تشير إجاباتك إلى أعراض اكتئاب خفيفة.',
                'moderate' => 'تشير إجاباتك إلى أعراض اكتئاب متوسطة.',
                'moderately_severe' => 'تشير إجاباتك إلى أعراض اكتئاب متوسطة إلى شديدة.',
                'severe' => 'تشير إجاباتك إلى أعراض اكتئاب شديدة.',
            ],
        ];

        return $descriptions[$language][$severity] ?? $descriptions['en'][$severity];
    }

    /**
     * Check if PHQ-9 item 9 (suicidal ideation) indicates crisis.
     */
    public function isCrisisIndicator(int $item9Score): bool
    {
        return $item9Score >= 2;
    }
}
