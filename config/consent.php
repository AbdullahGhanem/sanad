<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Consent Version
    |--------------------------------------------------------------------------
    |
    | The current version of the screening consent agreement. Bump this whenever
    | the consent terms change — students who agreed to an older version will be
    | asked to consent again before they can start a new screening.
    |
    */

    'version' => env('CONSENT_VERSION', '1.0'),

];
