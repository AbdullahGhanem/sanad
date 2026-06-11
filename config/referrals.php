<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Counseling Center Inbox
    |--------------------------------------------------------------------------
    |
    | An optional shared email address for the counseling center. When set, it
    | receives a copy of every referral alongside the in-app notifications sent
    | to counselor staff. Leave empty to notify counselors only.
    |
    */

    'center_email' => env('REFERRAL_CENTER_EMAIL'),

];
