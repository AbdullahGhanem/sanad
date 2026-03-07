<?php

namespace App\Services;

class CombinedScoringService
{
    public function __construct(
        private Phq9ScoringService $phq9Service,
        private Gad7ScoringService $gad7Service,
    ) {}

    /**
     * Calculate the combined severity from PHQ-9 and GAD-7 scores.
     * Uses the higher severity of the two instruments.
     */
    public function getCombinedSeverity(int $phq9Score, int $gad7Score): string
    {
        $severityOrder = ['minimal', 'mild', 'moderate', 'moderately_severe', 'severe'];

        $phq9Severity = $this->phq9Service->getSeverity($phq9Score);
        $gad7Severity = $this->gad7Service->getSeverity($gad7Score);

        $phq9Index = array_search($phq9Severity, $severityOrder);
        $gad7Index = array_search($gad7Severity, $severityOrder);

        return $severityOrder[max($phq9Index, $gad7Index)];
    }

    /**
     * Get the primary symptom cluster based on which score is higher.
     */
    public function getPrimaryCluster(int $phq9Score, int $gad7Score): string
    {
        if ($phq9Score > $gad7Score) {
            return 'depression';
        }

        if ($gad7Score > $phq9Score) {
            return 'anxiety';
        }

        return 'both';
    }

    /**
     * Get combined description in the given language.
     */
    public function getCombinedDescription(int $phq9Score, int $gad7Score, string $language = 'en'): string
    {
        $severity = $this->getCombinedSeverity($phq9Score, $gad7Score);

        $descriptions = [
            'en' => [
                'minimal' => 'Your overall mental health screening suggests minimal distress.',
                'mild' => 'Your overall screening suggests mild psychological distress.',
                'moderate' => 'Your overall screening suggests moderate psychological distress. Consider seeking support.',
                'moderately_severe' => 'Your overall screening suggests moderately severe distress. We recommend reaching out to a mental health professional.',
                'severe' => 'Your overall screening suggests severe distress. Please consider contacting a mental health professional or crisis service.',
            ],
            'ar' => [
                'minimal' => 'يشير الفحص العام لصحتك النفسية إلى ضائقة بسيطة.',
                'mild' => 'يشير الفحص العام إلى ضائقة نفسية خفيفة.',
                'moderate' => 'يشير الفحص العام إلى ضائقة نفسية متوسطة. فكر في طلب الدعم.',
                'moderately_severe' => 'يشير الفحص العام إلى ضائقة نفسية متوسطة إلى شديدة. نوصي بالتواصل مع متخصص في الصحة النفسية.',
                'severe' => 'يشير الفحص العام إلى ضائقة نفسية شديدة. يرجى التفكير في الاتصال بمتخصص أو خدمة أزمات.',
            ],
        ];

        return $descriptions[$language][$severity] ?? $descriptions['en'][$severity];
    }
}
