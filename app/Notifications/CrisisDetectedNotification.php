<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CrisisDetectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $source,
        public string $severity,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Real-time alert pushed to a counselor's admin panel the instant a crisis is detected.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->danger()
            ->icon(Heroicon::ExclamationTriangle)
            ->title('Crisis detected')
            ->body("A {$this->severity}-severity crisis was detected from {$this->source}. Review the crisis events dashboard.")
            ->getBroadcastMessage();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Crisis Event Detected — Sanad')
            ->greeting('Crisis Alert')
            ->line("A crisis event has been detected from **{$this->source}** with **{$this->severity}** severity.")
            ->line('Please review the crisis events dashboard for details.')
            ->action('View Crisis Events', url('/admin/crisis-events'))
            ->line('This is an automated alert from Sanad.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'source' => $this->source,
            'severity' => $this->severity,
            'message' => "Crisis detected from {$this->source} ({$this->severity} severity)",
        ];
    }
}
