<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Moderation
    |--------------------------------------------------------------------------
    |
    | When enabled, every assistant reply is passed through the Claude-based
    | ResponseModerator before it is shown to the student. Disable to fall back
    | to the deterministic rule checks only (useful in tests or to control cost).
    |
    */

    'ai_moderation' => env('GUARDRAIL_AI_MODERATION', true),

];
