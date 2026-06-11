<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ScreeningReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendScreeningReminders extends Command
{
    protected $signature = 'app:send-screening-reminders';

    protected $description = 'Nudge opted-in students who have not screened within the reminder interval';

    public function handle(): int
    {
        $cutoff = now()->subDays((int) config('reminders.interval_days'));
        $sent = 0;

        $this->dueStudents($cutoff)->chunkById(100, function ($users) use (&$sent): void {
            foreach ($users as $user) {
                $user->notify(new ScreeningReminderNotification);
                $user->forceFill(['last_reminded_at' => now()])->saveQuietly();
                $sent++;
            }
        });

        $this->info("Sent {$sent} screening reminder(s).");

        return self::SUCCESS;
    }

    /**
     * Students who opted in, screened before but not within the interval, and were
     * not already reminded within the same interval (the interval doubles as cooldown).
     *
     * @return Builder<User>
     */
    private function dueStudents(\Carbon\CarbonInterface $cutoff): Builder
    {
        return User::query()
            ->where('reminder_enabled', true)
            ->whereNotNull('email_verified_at')
            ->whereNotNull('last_screened_at')
            ->where('last_screened_at', '<', $cutoff)
            ->where(fn (Builder $query) => $query
                ->whereNull('last_reminded_at')
                ->orWhere('last_reminded_at', '<', $cutoff));
    }
}
