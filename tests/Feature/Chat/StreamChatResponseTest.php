<?php

namespace Tests\Feature\Chat;

use App\Ai\Agents\ResponseModerator;
use App\Ai\Agents\SanadChat;
use App\Jobs\StreamChatResponse;
use App\Services\ContextInjectionService;
use App\Services\ResponseGuardrailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamChatResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_channel_name_is_namespaced_by_chat_session(): void
    {
        $job = new StreamChatResponse('abc-123', null, 'hi', 'en');

        $this->assertSame('chat.abc-123', $job->channelName());
    }

    public function test_job_persists_the_assistant_message(): void
    {
        SanadChat::fake(['I hear you, and I am here to help.']);

        (new StreamChatResponse('session-uuid', null, 'I feel low', 'en'))
            ->handle(app(ContextInjectionService::class), app(ResponseGuardrailService::class));

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => 'session-uuid',
            'role' => 'assistant',
        ]);
    }

    public function test_safe_reply_is_persisted_unchanged(): void
    {
        SanadChat::fake(['You are not alone in this.']);

        (new StreamChatResponse('session-uuid', null, 'I feel low', 'en'))
            ->handle(app(ContextInjectionService::class), app(ResponseGuardrailService::class));

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => 'session-uuid',
            'role' => 'assistant',
            'content' => 'You are not alone in this.',
        ]);
    }

    public function test_rule_flagged_reply_is_replaced_with_safe_fallback(): void
    {
        SanadChat::fake(['You should take 50mg of sertraline every morning.']);

        (new StreamChatResponse('session-uuid', null, 'what medication helps?', 'en'))
            ->handle(app(ContextInjectionService::class), app(ResponseGuardrailService::class));

        $message = \App\Models\ChatMessage::where('session_id', 'session-uuid')->where('role', 'assistant')->first();

        $this->assertNotNull($message);
        $this->assertStringNotContainsString('50mg', $message->content);
        $this->assertStringContainsString('safe and helpful', $message->content);
    }

    public function test_ai_flagged_reply_is_replaced_with_safe_fallback(): void
    {
        config(['guardrail.ai_moderation' => true]);

        // Agent fakes are per-class, so each is faked independently.
        SanadChat::fake(['Some unsafe-sounding but rule-clean reply.']);
        ResponseModerator::fake([['safe' => false, 'categories' => ['harmful_advice'], 'reason' => 'unsafe']]);

        (new StreamChatResponse('session-uuid', null, 'I feel alone', 'en'))
            ->handle(app(ContextInjectionService::class), app(ResponseGuardrailService::class));

        $message = \App\Models\ChatMessage::where('session_id', 'session-uuid')->where('role', 'assistant')->first();

        $this->assertNotNull($message);
        $this->assertStringNotContainsString('unsafe-sounding', $message->content);
        $this->assertStringContainsString('safe and helpful', $message->content);
    }

    public function test_job_persists_fallback_when_generation_fails(): void
    {
        SanadChat::fake(function () {
            throw new \RuntimeException('provider down');
        });

        (new StreamChatResponse('session-uuid', null, 'hello', 'en'))
            ->handle(app(ContextInjectionService::class), app(ResponseGuardrailService::class));

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => 'session-uuid',
            'role' => 'assistant',
            'content' => 'Sorry, something went wrong. Please try again.',
        ]);
    }

    public function test_job_persists_arabic_fallback(): void
    {
        SanadChat::fake(function () {
            throw new \RuntimeException('provider down');
        });

        (new StreamChatResponse('session-uuid', null, 'مرحبا', 'ar'))
            ->handle(app(ContextInjectionService::class), app(ResponseGuardrailService::class));

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => 'session-uuid',
            'role' => 'assistant',
            'content' => 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.',
        ]);
    }
}
