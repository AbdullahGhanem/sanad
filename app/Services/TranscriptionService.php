<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Transcription;
use Throwable;

class TranscriptionService
{
    /**
     * Transcribe an uploaded audio clip to text using ElevenLabs speech-to-text.
     *
     * Returns null (degrading gracefully) when transcription is unavailable or fails,
     * so the chat simply falls back to typing.
     */
    public function transcribe(UploadedFile $audio, string $language = 'ar'): ?string
    {
        if (! Transcription::isFaked() && blank(config('ai.providers.elevenlabs.key'))) {
            Log::warning('Transcription unavailable: ELEVENLABS_API_KEY is not configured.');

            return null;
        }

        try {
            $response = Transcription::fromUpload($audio)
                ->language($language)
                ->generate(provider: Lab::ElevenLabs);

            $text = trim($response->text);

            return $text === '' ? null : $text;
        } catch (Throwable $e) {
            Log::warning('Transcription failed.', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
