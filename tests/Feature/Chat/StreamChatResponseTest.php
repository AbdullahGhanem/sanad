<?php

namespace Tests\Feature\Chat;

use App\Ai\Agents\SanadChat;
use App\Jobs\StreamChatResponse;
use App\Services\ContextInjectionService;
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
            ->handle(app(ContextInjectionService::class));

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => 'session-uuid',
            'role' => 'assistant',
        ]);
    }

    public function test_job_persists_fallback_when_generation_fails(): void
    {
        SanadChat::fake(function () {
            throw new \RuntimeException('provider down');
        });

        (new StreamChatResponse('session-uuid', null, 'hello', 'en'))
            ->handle(app(ContextInjectionService::class));

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
            ->handle(app(ContextInjectionService::class));

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => 'session-uuid',
            'role' => 'assistant',
            'content' => 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.',
        ]);
    }
}
