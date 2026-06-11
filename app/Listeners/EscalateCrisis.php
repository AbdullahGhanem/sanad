<?php

namespace App\Listeners;

use App\Events\CrisisDetected;
use App\Jobs\CrisisNotificationJob;

class EscalateCrisis
{
    /**
     * Escalate a detected crisis by notifying counselors.
     *
     * Runs synchronously so the queued notification job is dispatched as part of the
     * detection request; the heavy work (mail, broadcast) happens inside the queued job.
     */
    public function handle(CrisisDetected $event): void
    {
        CrisisNotificationJob::dispatch($event->source(), $event->severity());
    }
}
