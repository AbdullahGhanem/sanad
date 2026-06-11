<?php

namespace Tests\Feature\Chat;

use App\Livewire\Chat\ChatInterface;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatMessageMarkdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_rendered_content_converts_markdown_to_html(): void
    {
        $message = ChatMessage::factory()->create([
            'role' => 'assistant',
            'content' => "**اتصل بأقرب شخص تثق فيه**\n\n---\n\n- رقم الطوارئ: 123",
        ]);

        $html = $message->rendered_content;

        $this->assertStringContainsString('<strong>اتصل بأقرب شخص تثق فيه</strong>', $html);
        $this->assertStringContainsString('<hr', $html);
        $this->assertStringContainsString('<li>رقم الطوارئ: 123</li>', $html);
    }

    public function test_rendered_content_strips_raw_html_and_unsafe_links(): void
    {
        $message = ChatMessage::factory()->create([
            'role' => 'assistant',
            'content' => '<script>alert(1)</script> [click](javascript:alert(1))',
        ]);

        $html = $message->rendered_content;

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_assistant_messages_render_as_html_in_the_chat(): void
    {
        $message = ChatMessage::factory()->create([
            'role' => 'assistant',
            'content' => '**Call someone you trust**',
        ]);

        Livewire::test(ChatInterface::class)
            ->set('chatSessionId', $message->session_id)
            ->assertSeeHtml('<strong>Call someone you trust</strong>');
    }

    public function test_user_messages_remain_escaped_plain_text(): void
    {
        $message = ChatMessage::factory()->create([
            'role' => 'user',
            'content' => '**not bold** <b>nor this</b>',
        ]);

        Livewire::test(ChatInterface::class)
            ->set('chatSessionId', $message->session_id)
            ->assertDontSeeHtml('<strong>not bold</strong>')
            ->assertDontSeeHtml('<b>nor this</b>')
            ->assertSee('**not bold**');
    }
}
