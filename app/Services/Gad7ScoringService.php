<?php

namespace App\Services;

class Gad7ScoringService
{
    /**
     * GAD-7 severity thresholds.
     *
     * @var array<string, array{min: int, max: int}>
     */
    private const THRESHOLDS = [
        'minimal' => ['min' => 0, 'max' => 4],
        'mild' => ['min' => 5, 'max' => 9],
        'moderate' => ['min' => 10, 'max' => 14],
        'severe' => ['min' => 15, 'max' => 21],
    ];

    /**
     * Calculate the total GAD-7 score from answer values.
     *
     * @param  array<int>  $answerValues
     */
    public function calculateScore(array $answerValues): int
    {
        return min(array_sum($answerValues), 21);
    }

    /**
     * Get the severity label for a given GAD-7 score.
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
                'minimal' => 'Your responses suggest minimal anxiety symptoms.',
                'mild' => 'Your responses suggest mild anxiety symptoms.',
                'moderate' => 'Your responses suggest moderate anxiety symptoms.',
                'severe' => 'Your responses suggest severe anxiety symptoms.',
            ],
            'ar' => [
                'minimal' => 'تشير إجاباتك إلى أعراض قلق بسيطة.',
                'mild' => 'تشير إجاباتك إلى أعراض قلق خفيفة.',
                'moderate' => 'تشير إجاباتك إلى أعراض قلق متوسطة.',
                'severe' => 'تشير إجاباتك إلى أعراض قلق شديدة.',
            ],
        ];

        return $descriptions[$language][$severity] ?? $descriptions['en'][$severity];
    }
}
