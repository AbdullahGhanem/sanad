<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Re-screening Reminder Interval (days)
    |--------------------------------------------------------------------------
    |
    | A student who opted in (reminder_enabled) and last screened more than this
    | many days ago is nudged to check in again. The same interval is reused as
    | the cooldown, so a student is reminded at most once per interval.
    |
    */

    'interval_days' => env('REMINDER_INTERVAL_DAYS', 30),

];
