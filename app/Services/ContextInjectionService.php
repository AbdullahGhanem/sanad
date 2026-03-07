<?php

namespace App\Services;

use App\Models\ScreeningSession;

class ContextInjectionService
{
    public function __construct(
        private Phq9ScoringService $phq9Service,
        private Gad7ScoringService $gad7Service,
    ) {}

    /**
     * Convert screening session results into a natural language context string for the chatbot.
     */
    public function buildContext(ScreeningSession $session, string $language = 'en'): string
    {
        $phq9Severity = $this->phq9Service->getSeverity($session->phq9_score);
        $gad7Severity = $this->gad7Service->getSeverity($session->gad7_score);

        if ($language === 'ar') {
            return $this->buildArabicContext($session, $phq9Severity, $gad7Severity);
        }

        return $this->buildEnglishContext($session, $phq9Severity, $gad7Severity);
    }

    private function buildEnglishContext(ScreeningSession $session, string $phq9Severity, string $gad7Severity): string
    {
        $depressionLabel = str_replace('_', ' ', $phq9Severity);
        $anxietyLabel = str_replace('_', ' ', $gad7Severity);

        $context = 'The student recently completed a mental health screening. ';
        $context .= "Their depression screening (PHQ-9) indicates {$depressionLabel} symptoms (score: {$session->phq9_score}/27). ";
        $context .= "Their anxiety screening (GAD-7) indicates {$anxietyLabel} symptoms (score: {$session->gad7_score}/21). ";
        $context .= "Overall severity: {$session->combined_severity}.";

        if ($session->nlp_classification) {
            $context .= " Free-text analysis suggests {$session->nlp_classification} distress level.";
        }

        return $context;
    }

    private function buildArabicContext(ScreeningSession $session, string $phq9Severity, string $gad7Severity): string
    {
        $severityAr = [
            'minimal' => 'بسيطة',
            'mild' => 'خفيفة',
            'moderate' => 'متوسطة',
            'moderately_severe' => 'متوسطة إلى شديدة',
            'severe' => 'شديدة',
        ];

        $depressionLabel = $severityAr[$phq9Severity] ?? $phq9Severity;
        $anxietyLabel = $severityAr[$gad7Severity] ?? $gad7Severity;

        $context = 'أكمل الطالب مؤخراً فحصاً للصحة النفسية. ';
        $context .= "يشير فحص الاكتئاب (PHQ-9) إلى أعراض {$depressionLabel} (الدرجة: {$session->phq9_score}/27). ";
        $context .= "يشير فحص القلق (GAD-7) إلى أعراض {$anxietyLabel} (الدرجة: {$session->gad7_score}/21). ";
        $context .= 'الشدة العامة: '.($severityAr[$session->combined_severity] ?? $session->combined_severity).'.';

        return $context;
    }
}
