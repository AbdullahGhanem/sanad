<?php

namespace Tests\Feature\Ai;

use App\Ai\Agents\ResponseModerator;
use App\Services\ResponseGuardrailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Tests\TestCase;

class ResponseGuardrailServiceTest extends TestCase
{
    use RefreshDatabase;

    private ResponseGuardrailService $guardrail;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guardrail = new ResponseGuardrailService;
    }

    public function test_empty_reply_is_safe(): void
    {
        $verdict = $this->guardrail->check('   ', 'I feel sad', 'en');

        $this->assertTrue($verdict->safe);
        $this->assertSame('empty', $verdict->source);
    }

    public function test_dosage_rule_blocks_reply_without_calling_ai(): void
    {
        $verdict = $this->guardrail->check('You can take 50mg of that twice a day.', 'what should I take?', 'en');

        $this->assertTrue($verdict->blocked());
        $this->assertSame('rules', $verdict->source);
        $this->assertContains('medication_dosage', $verdict->categories);
    }

    public function test_normal_reply_is_safe_when_ai_moderation_disabled(): void
    {
        config(['guardrail.ai_moderation' => false]);

        $verdict = $this->guardrail->check('You are not alone, and reaching out took courage.', 'I feel low', 'en');

        $this->assertTrue($verdict->safe);
        $this->assertSame('ai_disabled', $verdict->source);
    }

    public function test_ai_moderator_can_mark_reply_safe(): void
    {
        config(['guardrail.ai_moderation' => true]);
        ResponseModerator::fake([['safe' => true, 'categories' => [], 'reason' => 'supportive']]);

        $verdict = $this->guardrail->check('Take a slow breath; I am here with you.', 'I am panicking', 'en');

        $this->assertTrue($verdict->safe);
        $this->assertSame('ai', $verdict->source);
    }

    public function test_ai_moderator_can_block_reply(): void
    {
        config(['guardrail.ai_moderation' => true]);
        ResponseModerator::fake([['safe' => false, 'categories' => ['harmful_advice'], 'reason' => 'dangerous']]);

        $verdict = $this->guardrail->check('Just ignore everyone and isolate yourself.', 'I feel alone', 'en');

        $this->assertTrue($verdict->blocked());
        $this->assertSame('ai', $verdict->source);
        $this->assertContains('harmful_advice', $verdict->categories);
        $this->assertSame('dangerous', $verdict->reason);
    }

    public function test_fails_open_when_ai_unavailable(): void
    {
        config(['guardrail.ai_moderation' => true]);
        config(['ai.providers.anthropic.key' => null]);

        $verdict = $this->guardrail->check('You matter, and support is available.', 'hello', 'en');

        $this->assertTrue($verdict->safe);
        $this->assertSame('ai_unavailable', $verdict->source);
    }

    public function test_moderator_schema_exposes_safe_categories_and_reason(): void
    {
        $schema = (new ResponseModerator)->schema(new JsonSchemaTypeFactory);

        $this->assertArrayHasKey('safe', $schema);
        $this->assertArrayHasKey('categories', $schema);
        $this->assertArrayHasKey('reason', $schema);
    }
}
