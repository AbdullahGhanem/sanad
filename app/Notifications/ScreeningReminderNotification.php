<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScreeningReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A gentle check-in from Sanad')
            ->greeting('Hi '.($notifiable->name ?? '').',')
            ->line("It's been a while since your last mental-health check-in. Taking a few minutes to screen again can help you notice how you're doing over time.")
            ->action('Take a screening', route('screening'))
            ->line('You can turn these reminders off any time in your settings. You are not alone — support is always here.');
    }
}
