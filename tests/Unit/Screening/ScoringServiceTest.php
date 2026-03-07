<?php

namespace Tests\Unit\Screening;

use App\Services\CombinedScoringService;
use App\Services\Gad7ScoringService;
use App\Services\Phq9ScoringService;
use PHPUnit\Framework\TestCase;

class ScoringServiceTest extends TestCase
{
    private Phq9ScoringService $phq9Service;

    private Gad7ScoringService $gad7Service;

    private CombinedScoringService $combinedService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phq9Service = new Phq9ScoringService;
        $this->gad7Service = new Gad7ScoringService;
        $this->combinedService = new CombinedScoringService($this->phq9Service, $this->gad7Service);
    }

    public function test_phq9_calculates_score_correctly(): void
    {
        $this->assertSame(0, $this->phq9Service->calculateScore([0, 0, 0, 0, 0, 0, 0, 0, 0]));
        $this->assertSame(9, $this->phq9Service->calculateScore([1, 1, 1, 1, 1, 1, 1, 1, 1]));
        $this->assertSame(27, $this->phq9Service->calculateScore([3, 3, 3, 3, 3, 3, 3, 3, 3]));
    }

    public function test_phq9_caps_score_at27(): void
    {
        $this->assertSame(27, $this->phq9Service->calculateScore([3, 3, 3, 3, 3, 3, 3, 3, 3]));
    }

    public function test_phq9_minimal_severity(): void
    {
        $this->assertSame('minimal', $this->phq9Service->getSeverity(0));
        $this->assertSame('minimal', $this->phq9Service->getSeverity(4));
    }

    public function test_phq9_mild_severity(): void
    {
        $this->assertSame('mild', $this->phq9Service->getSeverity(5));
        $this->assertSame('mild', $this->phq9Service->getSeverity(9));
    }

    public function test_phq9_moderate_severity(): void
    {
        $this->assertSame('moderate', $this->phq9Service->getSeverity(10));
        $this->assertSame('moderate', $this->phq9Service->getSeverity(14));
    }

    public function test_phq9_moderately_severe_severity(): void
    {
        $this->assertSame('moderately_severe', $this->phq9Service->getSeverity(15));
        $this->assertSame('moderately_severe', $this->phq9Service->getSeverity(19));
    }

    public function test_phq9_severe_severity(): void
    {
        $this->assertSame('severe', $this->phq9Service->getSeverity(20));
        $this->assertSame('severe', $this->phq9Service->getSeverity(27));
    }

    public function test_phq9_crisis_indicator_triggers_at_score_two(): void
    {
        $this->assertFalse($this->phq9Service->isCrisisIndicator(0));
        $this->assertFalse($this->phq9Service->isCrisisIndicator(1));
        $this->assertTrue($this->phq9Service->isCrisisIndicator(2));
        $this->assertTrue($this->phq9Service->isCrisisIndicator(3));
    }

    public function test_phq9_returns_english_description(): void
    {
        $description = $this->phq9Service->getSeverityDescription(3, 'en');
        $this->assertStringContainsString('minimal', $description);
    }

    public function test_phq9_returns_arabic_description(): void
    {
        $description = $this->phq9Service->getSeverityDescription(3, 'ar');
        $this->assertStringContainsString('بسيطة', $description);
    }

    public function test_gad7_calculates_score_correctly(): void
    {
        $this->assertSame(0, $this->gad7Service->calculateScore([0, 0, 0, 0, 0, 0, 0]));
        $this->assertSame(7, $this->gad7Service->calculateScore([1, 1, 1, 1, 1, 1, 1]));
        $this->assertSame(21, $this->gad7Service->calculateScore([3, 3, 3, 3, 3, 3, 3]));
    }

    public function test_gad7_caps_score_at21(): void
    {
        $this->assertSame(21, $this->gad7Service->calculateScore([3, 3, 3, 3, 3, 3, 3]));
    }

    public function test_gad7_minimal_severity(): void
    {
        $this->assertSame('minimal', $this->gad7Service->getSeverity(0));
        $this->assertSame('minimal', $this->gad7Service->getSeverity(4));
    }

    public function test_gad7_mild_severity(): void
    {
        $this->assertSame('mild', $this->gad7Service->getSeverity(5));
        $this->assertSame('mild', $this->gad7Service->getSeverity(9));
    }

    public function test_gad7_moderate_severity(): void
    {
        $this->assertSame('moderate', $this->gad7Service->getSeverity(10));
        $this->assertSame('moderate', $this->gad7Service->getSeverity(14));
    }

    public function test_gad7_severe_severity(): void
    {
        $this->assertSame('severe', $this->gad7Service->getSeverity(15));
        $this->assertSame('severe', $this->gad7Service->getSeverity(21));
    }

    public function test_combined_severity_uses_higher_score(): void
    {
        $this->assertSame('severe', $this->combinedService->getCombinedSeverity(20, 3));
        $this->assertSame('severe', $this->combinedService->getCombinedSeverity(3, 15));
    }

    public function test_combined_severity_both_minimal(): void
    {
        $this->assertSame('minimal', $this->combinedService->getCombinedSeverity(0, 0));
    }

    public function test_primary_cluster_depression(): void
    {
        $this->assertSame('depression', $this->combinedService->getPrimaryCluster(15, 5));
    }

    public function test_primary_cluster_anxiety(): void
    {
        $this->assertSame('anxiety', $this->combinedService->getPrimaryCluster(5, 15));
    }

    public function test_primary_cluster_both(): void
    {
        $this->assertSame('both', $this->combinedService->getPrimaryCluster(10, 10));
    }

    public function test_combined_description_english(): void
    {
        $description = $this->combinedService->getCombinedDescription(0, 0, 'en');
        $this->assertStringContainsString('minimal', $description);
    }

    public function test_combined_description_arabic(): void
    {
        $description = $this->combinedService->getCombinedDescription(0, 0, 'ar');
        $this->assertStringContainsString('بسيطة', $description);
    }
}
