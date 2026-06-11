<?php

namespace App\Notifications;

use App\Models\Referral;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Referral $referral,
    ) {}

    /**
     * In-app channels only apply to counselor user accounts; a shared inbox gets mail.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable instanceof User
            ? ['mail', 'database', 'broadcast']
            : ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $severity = $this->referral->crisisEvent?->severity ?? 'unknown';

        $mail = (new MailMessage)
            ->subject('New counseling referral — Sanad')
            ->greeting('New referral')
            ->line("A student has been referred to the counseling center ({$severity} severity).");

        if ($reason = $this->referral->reason) {
            $mail->line("Reason: {$reason}");
        }

        return $mail
            ->action('Review referrals', url('/admin/referrals'))
            ->line('Please review and schedule an appointment.');
    }

    /**
     * Real-time toast in the counselor's admin panel.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $severity = $this->referral->crisisEvent?->severity ?? 'unknown';

        return FilamentNotification::make()
            ->info()
            ->icon(Heroicon::ArrowTopRightOnSquare)
            ->title('New referral')
            ->body("A {$severity}-severity case was referred to the counseling center.")
            ->getBroadcastMessage();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'referral_id' => $this->referral->id,
            'severity' => $this->referral->crisisEvent?->severity,
            'message' => 'A student was referred to the counseling center.',
        ];
    }
}
