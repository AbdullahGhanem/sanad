<?php

namespace Tests\Feature\Counselor;

use App\Models\CrisisEvent;
use App\Models\Referral;
use App\Models\User;
use App\Notifications\ReferralCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReferralNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_other_counselors_are_notified_but_not_the_referrer_or_students(): void
    {
        Notification::fake();

        $referrer = User::factory()->counselor()->create();
        $other = User::factory()->counselor()->create();
        $student = User::factory()->create(['role' => 'student']);

        Referral::factory()->create([
            'crisis_event_id' => CrisisEvent::factory()->create()->id,
            'referred_by_id' => $referrer->id,
        ]);

        Notification::assertSentTo($other, ReferralCreatedNotification::class);
        Notification::assertNotSentTo($referrer, ReferralCreatedNotification::class);
        Notification::assertNotSentTo($student, ReferralCreatedNotification::class);
    }

    public function test_center_inbox_is_notified_when_configured(): void
    {
        config(['referrals.center_email' => 'center@university.test']);
        Notification::fake();

        Referral::factory()->create(['referred_by_id' => User::factory()->counselor()->create()->id]);

        Notification::assertSentOnDemand(
            ReferralCreatedNotification::class,
            fn ($notification, array $channels, AnonymousNotifiable $notifiable): bool => ($notifiable->routes['mail'] ?? null) === 'center@university.test',
        );
    }

    public function test_center_inbox_is_not_notified_when_unset(): void
    {
        config(['referrals.center_email' => null]);
        Notification::fake();

        // Only the referrer counselor exists, so no in-app recipients and no inbox.
        Referral::factory()->create(['referred_by_id' => User::factory()->counselor()->create()->id]);

        Notification::assertNothingSent();
    }

    public function test_channels_adapt_to_the_notifiable(): void
    {
        $notification = new ReferralCreatedNotification(Referral::factory()->make());

        $this->assertSame(['mail', 'database', 'broadcast'], $notification->via(new User));
        $this->assertSame(['mail'], $notification->via(new AnonymousNotifiable));
    }
}
