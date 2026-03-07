<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\CrisisDetectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CrisisNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $source,
        public string $severity,
    ) {}

    public function handle(): void
    {
        $admins = User::where('role', 'super_admin')
            ->orWhere('role', 'university_admin')
            ->get();

        Log::channel('single')->warning('Crisis event detected', [
            'source' => $this->source,
            'severity' => $this->severity,
            'admin_count' => $admins->count(),
        ]);

        Notification::send($admins, new CrisisDetectedNotification($this->source, $this->severity));
    }
}
