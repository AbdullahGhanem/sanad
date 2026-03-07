<?php

namespace Tests\Feature\Services;

use App\Models\CrisisKeyword;
use App\Services\CrisisDetectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrisisDetectionServiceTest extends TestCase
{
    use RefreshDatabase;

    private CrisisDetectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CrisisDetectionService::class);

        CrisisKeyword::create(['phrase' => 'kill myself', 'language' => 'en', 'is_active' => true]);
        CrisisKeyword::create(['phrase' => 'suicide', 'language' => 'en', 'is_active' => true]);
        CrisisKeyword::create(['phrase' => 'انتحار', 'language' => 'ar', 'is_active' => true]);
        CrisisKeyword::create(['phrase' => 'أقتل نفسي', 'language' => 'ar', 'is_active' => true]);
    }

    public function test_detects_english_crisis_keyword(): void
    {
        $this->assertTrue($this->service->detectCrisis('I want to kill myself', 'en'));
    }

    public function test_detects_arabic_crisis_keyword(): void
    {
        $this->assertTrue($this->service->detectCrisis('أفكر في انتحار', 'ar'));
    }

    public function test_returns_false_for_safe_text(): void
    {
        $this->assertFalse($this->service->detectCrisis('I feel a bit sad today', 'en'));
    }

    public function test_case_insensitive_matching(): void
    {
        $this->assertTrue($this->service->detectCrisis('I want to KILL MYSELF', 'en'));
    }

    public function test_detects_crisis_any_language(): void
    {
        $this->assertTrue($this->service->detectCrisisAnyLanguage('I want to kill myself'));
        $this->assertTrue($this->service->detectCrisisAnyLanguage('أفكر في انتحار'));
        $this->assertFalse($this->service->detectCrisisAnyLanguage('I feel happy today'));
    }

    public function test_inactive_keywords_are_ignored(): void
    {
        CrisisKeyword::create(['phrase' => 'end my life', 'language' => 'en', 'is_active' => false]);

        $this->assertFalse($this->service->detectCrisis('I want to end my life', 'en'));
    }

    public function test_logs_crisis_event(): void
    {
        $event = $this->service->logCrisisEvent('test-uuid-123', 'chat');

        $this->assertDatabaseHas('crisis_events', [
            'anonymous_id' => 'test-uuid-123',
            'source' => 'chat',
            'severity' => 'high',
        ]);
    }

    public function test_empty_text_returns_false(): void
    {
        $this->assertFalse($this->service->detectCrisis('', 'en'));
    }
}
