<?php

namespace App\Services;

use App\Events\CrisisDetected;
use App\Models\CrisisEvent;
use App\Models\CrisisKeyword;

class CrisisDetectionService
{
    /**
     * Check if the given text contains any active crisis keywords.
     */
    public function detectCrisis(string $text, string $language): bool
    {
        $keywords = CrisisKeyword::where('is_active', true)
            ->where('language', $language)
            ->pluck('phrase');

        $normalizedText = mb_strtolower(trim($text));

        foreach ($keywords as $phrase) {
            if (mb_strpos($normalizedText, mb_strtolower($phrase)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check text in both languages for crisis indicators.
     */
    public function detectCrisisAnyLanguage(string $text): bool
    {
        return $this->detectCrisis($text, 'en') || $this->detectCrisis($text, 'ar');
    }

    /**
     * Record a crisis event and dispatch the decoupled escalation pipeline.
     */
    public function logCrisisEvent(string $anonymousId, string $source = 'chat', ?string $language = null): CrisisEvent
    {
        $event = CrisisEvent::create([
            'anonymous_id' => $anonymousId,
            'source' => $source,
            'severity' => 'high',
        ]);

        CrisisDetected::dispatch($event, $language);

        return $event;
    }
}
