<?php

namespace App\Services;

class EnsembleScoringService
{
    private const QUESTIONNAIRE_WEIGHT = 0.7;

    private const NLP_WEIGHT = 0.3;

    /**
     * Severity levels mapped to numeric values for weighted calculation.
     *
     * @var array<string, int>
     */
    private const SEVERITY_MAP = [
        'minimal' => 0,
        'mild' => 1,
        'moderate' => 2,
        'moderately_severe' => 3,
        'severe' => 4,
    ];

    /**
     * Reverse map from numeric to severity label.
     *
     * @var array<int, string>
     */
    private const SEVERITY_LABELS = [
        0 => 'minimal',
        1 => 'mild',
        2 => 'moderate',
        3 => 'moderately_severe',
        4 => 'severe',
    ];

    /**
     * Merge questionnaire severity with NLP severity using weighted scoring (70/30).
     */
    public function merge(string $questionnaireSeverity, string $nlpSeverity, float $nlpConfidence): string
    {
        $questionnaireValue = self::SEVERITY_MAP[$questionnaireSeverity] ?? 0;
        $nlpValue = self::SEVERITY_MAP[$nlpSeverity] ?? 0;

        $adjustedNlpWeight = self::NLP_WEIGHT * $nlpConfidence;
        $adjustedQuestionnaireWeight = self::QUESTIONNAIRE_WEIGHT + (self::NLP_WEIGHT * (1 - $nlpConfidence));

        $weightedScore = ($questionnaireValue * $adjustedQuestionnaireWeight) + ($nlpValue * $adjustedNlpWeight);

        $roundedIndex = (int) round($weightedScore);
        $roundedIndex = max(0, min(4, $roundedIndex));

        return self::SEVERITY_LABELS[$roundedIndex];
    }

    /**
     * Determine if NLP result should upgrade severity (only upgrades, never downgrades).
     */
    public function mergeConservative(string $questionnaireSeverity, string $nlpSeverity, float $nlpConfidence): string
    {
        $merged = $this->merge($questionnaireSeverity, $nlpSeverity, $nlpConfidence);

        $questionnaireValue = self::SEVERITY_MAP[$questionnaireSeverity] ?? 0;
        $mergedValue = self::SEVERITY_MAP[$merged] ?? 0;

        return self::SEVERITY_LABELS[max($questionnaireValue, $mergedValue)];
    }
}
