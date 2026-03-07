<?php

namespace Tests\Feature\Chat;

use App\Ai\Agents\SanadChat;
use App\Livewire\Chat\ChatInterface;
use App\Models\CrisisKeyword;
use App\Models\ScreeningSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatInterfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_page_renders(): void
    {
        $this->get(route('chat'))
            ->assertOk();
    }

    public function test_chat_page_renders_with_session(): void
    {
        $session = ScreeningSession::factory()->create();

        $this->get(route('chat', $session->id))
            ->assertOk();
    }

    public function test_chat_initializes_with_session_id(): void
    {
        Livewire::test(ChatInterface::class)
            ->assertSet('language', 'ar')
            ->assertNotSet('chatSessionId', '');
    }

    public function test_send_message_saves_user_message(): void
    {
        SanadChat::fake(['I understand how you feel.']);

        Livewire::test(ChatInterface::class)
            ->set('message', 'I feel sad today')
            ->call('sendMessage');

        $this->assertDatabaseHas('chat_messages', [
            'role' => 'user',
            'content' => 'I feel sad today',
            'language' => 'en',
        ]);
    }

    public function test_send_message_saves_assistant_response(): void
    {
        SanadChat::fake(['I understand how you feel.']);

        Livewire::test(ChatInterface::class)
            ->set('message', 'I feel sad today')
            ->call('sendMessage');

        $this->assertDatabaseHas('chat_messages', [
            'role' => 'assistant',
        ]);
    }

    public function test_send_message_validates_required(): void
    {
        Livewire::test(ChatInterface::class)
            ->set('message', '')
            ->call('sendMessage')
            ->assertHasErrors(['message']);
    }

    public function test_send_message_validates_max_length(): void
    {
        Livewire::test(ChatInterface::class)
            ->set('message', str_repeat('a', 1001))
            ->call('sendMessage')
            ->assertHasErrors(['message']);
    }

    public function test_detects_arabic_language_from_message(): void
    {
        SanadChat::fake(['أنا أفهم شعورك']);

        $component = Livewire::test(ChatInterface::class)
            ->set('message', 'أشعر بالحزن اليوم')
            ->call('sendMessage');

        $component->assertSet('language', 'ar');
    }

    public function test_crisis_keyword_trigges_overlay(): void
    {
        SanadChat::fake(['I am concerned about your safety.']);
        CrisisKeyword::create(['phrase' => 'kill myself', 'language' => 'en', 'is_active' => true]);

        Livewire::test(ChatInterface::class)
            ->set('message', 'I want to kill myself')
            ->call('sendMessage')
            ->assertSet('showCrisisOverlay', true);

        $this->assertDatabaseHas('chat_messages', [
            'role' => 'user',
            'detected_crisis' => true,
        ]);

        $this->assertDatabaseHas('crisis_events', [
            'source' => 'chat',
            'severity' => 'high',
        ]);
    }

    public function test_acknowledge_crisis_dismisses_overlay(): void
    {
        SanadChat::fake(['I am concerned about your safety.']);
        CrisisKeyword::create(['phrase' => 'kill myself', 'language' => 'en', 'is_active' => true]);

        Livewire::test(ChatInterface::class)
            ->set('message', 'I want to kill myself')
            ->call('sendMessage')
            ->assertSet('showCrisisOverlay', true)
            ->call('acknowledgeCrisis')
            ->assertSet('showCrisisOverlay', false);
    }

    public function test_switch_language(): void
    {
        Livewire::test(ChatInterface::class)
            ->call('switchLanguage', 'ar')
            ->assertSet('language', 'ar')
            ->call('switchLanguage', 'en')
            ->assertSet('language', 'en');
    }

    public function test_screening_context_passed_to_chat(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 15,
            'gad7_score' => 10,
            'combined_severity' => 'moderately_severe',
        ]);

        SanadChat::fake(['Based on your screening results...']);

        Livewire::test(ChatInterface::class, ['session' => $session->id])
            ->assertSet('screeningSessionId', $session->id)
            ->set('message', 'Can you help me?')
            ->call('sendMessage');

        $this->assertDatabaseHas('chat_messages', [
            'role' => 'assistant',
            'screening_session_id' => $session->id,
        ]);
    }

    public function test_fallback_response_on_ai_failure(): void
    {
        SanadChat::fake(function () {
            throw new \Exception('API error');
        });

        Livewire::test(ChatInterface::class)
            ->set('message', 'Hello')
            ->call('sendMessage');

        $this->assertDatabaseHas('chat_messages', [
            'role' => 'assistant',
            'content' => 'Sorry, something went wrong. Please try again.',
        ]);
    }
}
