<?php

namespace Tests\Feature\Services;

use App\Models\ScreeningSession;
use App\Services\ContextInjectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContextInjectionServiceTest extends TestCase
{
    use RefreshDatabase;

    private ContextInjectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ContextInjectionService::class);
    }

    public function test_builds_english_context(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 14,
            'gad7_score' => 8,
            'combined_severity' => 'moderate',
        ]);

        $context = $this->service->buildContext($session, 'en');

        $this->assertStringContainsString('moderate', $context);
        $this->assertStringContainsString('14/27', $context);
        $this->assertStringContainsString('8/21', $context);
        $this->assertStringContainsString('PHQ-9', $context);
        $this->assertStringContainsString('GAD-7', $context);
    }

    public function test_builds_arabic_context(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 5,
            'gad7_score' => 3,
            'combined_severity' => 'mild',
        ]);

        $context = $this->service->buildContext($session, 'ar');

        $this->assertStringContainsString('خفيفة', $context);
        $this->assertStringContainsString('5/27', $context);
        $this->assertStringContainsString('3/21', $context);
    }

    public function test_includes_nlp_classification_when_present(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 10,
            'gad7_score' => 10,
            'combined_severity' => 'moderate',
            'nlp_classification' => 'moderate',
        ]);

        $context = $this->service->buildContext($session, 'en');

        $this->assertStringContainsString('Free-text analysis', $context);
        $this->assertStringContainsString('moderate', $context);
    }

    public function test_excludes_nlp_classification_when_null(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 0,
            'gad7_score' => 0,
            'combined_severity' => 'minimal',
            'nlp_classification' => null,
        ]);

        $context = $this->service->buildContext($session, 'en');

        $this->assertStringNotContainsString('Free-text analysis', $context);
    }

    public function test_minimal_scores_produce_minimal_context(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 0,
            'gad7_score' => 0,
            'combined_severity' => 'minimal',
        ]);

        $context = $this->service->buildContext($session, 'en');

        $this->assertStringContainsString('minimal', $context);
    }

    public function test_severe_scores_produce_severe_context(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 25,
            'gad7_score' => 18,
            'combined_severity' => 'severe',
        ]);

        $context = $this->service->buildContext($session, 'en');

        $this->assertStringContainsString('severe', $context);
    }
}
