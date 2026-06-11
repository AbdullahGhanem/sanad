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
            ->subject(__('reminders.subject'))
            ->greeting(__('reminders.greeting', ['name' => $notifiable->name ?? '']))
            ->line(__('reminders.intro'))
            ->action(__('reminders.action'), route('screening'))
            ->line(__('reminders.footer'));
    }
}
