<?php

namespace Tests\Feature\Ai;

use App\Services\TranscriptionService;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Transcription;
use Tests\TestCase;

class TranscriptionServiceTest extends TestCase
{
    private function audio(): UploadedFile
    {
        return UploadedFile::fake()->create('voice.webm', 40, 'audio/webm');
    }

    public function test_transcribes_audio_to_text(): void
    {
        Transcription::fake(['مرحبا، أشعر بالقلق هذا الأسبوع']);

        $text = (new TranscriptionService)->transcribe($this->audio(), 'ar');

        $this->assertSame('مرحبا، أشعر بالقلق هذا الأسبوع', $text);
    }

    public function test_returns_null_when_transcription_unavailable(): void
    {
        config(['ai.providers.elevenlabs.key' => null]);

        $text = (new TranscriptionService)->transcribe($this->audio(), 'ar');

        $this->assertNull($text);
    }

    public function test_returns_null_for_empty_transcription(): void
    {
        Transcription::fake(['   ']);

        $text = (new TranscriptionService)->transcribe($this->audio(), 'en');

        $this->assertNull($text);
    }
}
