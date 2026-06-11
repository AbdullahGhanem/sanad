<?php

namespace Tests\Feature\Chat;

use App\Livewire\Chat\ChatInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Transcription;
use Livewire\Livewire;
use Tests\TestCase;

class ChatVoiceInputTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploaded_audio_is_transcribed_into_the_message_input(): void
    {
        Transcription::fake(['I have been feeling really anxious lately.']);

        Livewire::test(ChatInterface::class)
            ->set('audioFile', UploadedFile::fake()->create('voice.webm', 50, 'audio/webm'))
            ->assertSet('message', 'I have been feeling really anxious lately.')
            ->assertSet('audioFile', null)
            ->assertDispatched('transcription-ready');
    }

    public function test_failed_transcription_leaves_message_empty_and_signals_failure(): void
    {
        // No fake + no key => transcription unavailable => null.
        config(['ai.providers.elevenlabs.key' => null]);

        Livewire::test(ChatInterface::class)
            ->set('audioFile', UploadedFile::fake()->create('voice.webm', 50, 'audio/webm'))
            ->assertSet('message', '')
            ->assertDispatched('transcription-failed');
    }

    public function test_non_audio_upload_is_rejected(): void
    {
        Livewire::test(ChatInterface::class)
            ->set('audioFile', UploadedFile::fake()->create('malware.php', 10, 'text/x-php'))
            ->assertHasErrors('audioFile');
    }
}
