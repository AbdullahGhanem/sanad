<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Data Retention (days)
    |--------------------------------------------------------------------------
    |
    | How long anonymous records are kept before the scheduled purge removes
    | them. A value of null means "never auto-purge" — used for records under a
    | safety or legal hold. Deleting a screening session cascades to its answers
    | and any chat messages tied to it.
    |
    | These are policy decisions: review them against your institution's data
    | protection requirements before relying on them.
    |
    */

    'screening_sessions' => env('RETENTION_SCREENING_DAYS', 365),

    'chat_messages' => env('RETENTION_CHAT_DAYS', 180),

    // Kept by default — crisis events may be subject to a safety/legal hold.
    'crisis_events' => env('RETENTION_CRISIS_DAYS'),

    // Kept by default — consent records are the legal proof of agreement.
    'consent_records' => env('RETENTION_CONSENT_DAYS'),

];
