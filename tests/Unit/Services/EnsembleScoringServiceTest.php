<?php

namespace Tests\Unit\Services;

use App\Services\EnsembleScoringService;
use PHPUnit\Framework\TestCase;

class EnsembleScoringServiceTest extends TestCase
{
    private EnsembleScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EnsembleScoringService;
    }

    public function test_merge_same_severity_returns_same(): void
    {
        $this->assertSame('minimal', $this->service->merge('minimal', 'minimal', 1.0));
        $this->assertSame('severe', $this->service->merge('severe', 'severe', 1.0));
    }

    public function test_merge_questionnaire_dominates_at70_percent(): void
    {
        // Questionnaire: severe (4), NLP: minimal (0), confidence 1.0
        // Weighted: 4*0.7 + 0*0.3 = 2.8 → round to 3 = moderately_severe
        $this->assertSame('moderately_severe', $this->service->merge('severe', 'minimal', 1.0));
    }

    public function test_merge_nlp_influences_result(): void
    {
        // Questionnaire: minimal (0), NLP: severe (4), confidence 1.0
        // Weighted: 0*0.7 + 4*0.3 = 1.2 → round to 1 = mild
        $this->assertSame('mild', $this->service->merge('minimal', 'severe', 1.0));
    }

    public function test_merge_low_confidence_reduces_nlp_weight(): void
    {
        // Questionnaire: minimal (0), NLP: severe (4), confidence 0.0
        // NLP weight = 0.3 * 0.0 = 0, questionnaire weight = 0.7 + 0.3 = 1.0
        // Weighted: 0*1.0 + 4*0.0 = 0 → minimal
        $this->assertSame('minimal', $this->service->merge('minimal', 'severe', 0.0));
    }

    public function test_merge_middle_values(): void
    {
        // Questionnaire: mild (1), NLP: moderate (2), confidence 1.0
        // Weighted: 1*0.7 + 2*0.3 = 1.3 → round to 1 = mild
        $this->assertSame('mild', $this->service->merge('mild', 'moderate', 1.0));
    }

    public function test_conservative_merge_never_downgrades(): void
    {
        // merge('moderate', 'minimal', 1.0) would give mild (2*0.7+0*0.3=1.4→1)
        // but conservative ensures at least 'moderate'
        $this->assertSame('moderate', $this->service->mergeConservative('moderate', 'minimal', 1.0));
    }

    public function test_conservative_merge_can_upgrade(): void
    {
        // merge('mild', 'severe', 1.0) = 1*0.7+4*0.3=1.9→2=moderate
        // conservative: max(mild=1, moderate=2) = moderate (upgrade from mild)
        $this->assertSame('moderate', $this->service->mergeConservative('mild', 'severe', 1.0));
    }

    public function test_merge_handles_invalid_severity(): void
    {
        // Invalid severity defaults to 0
        $this->assertSame('minimal', $this->service->merge('unknown', 'unknown', 1.0));
    }
}
