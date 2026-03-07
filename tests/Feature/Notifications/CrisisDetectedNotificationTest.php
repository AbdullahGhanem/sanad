<?php

namespace Tests\Feature\Notifications;

use App\Jobs\CrisisNotificationJob;
use App\Models\User;
use App\Notifications\CrisisDetectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CrisisDetectedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_crisis_notification_sent_to_admins(): void
    {
        Notification::fake();

        $superAdmin = User::factory()->superAdmin()->create();
        $universityAdmin = User::factory()->universityAdmin()->create();
        User::factory()->create(['role' => 'student']);

        $job = new CrisisNotificationJob('chat', 'high');
        $job->handle();

        Notification::assertSentTo($superAdmin, CrisisDetectedNotification::class);
        Notification::assertSentTo($universityAdmin, CrisisDetectedNotification::class);
    }

    public function test_crisis_notification_not_sent_to_students(): void
    {
        Notification::fake();

        $student = User::factory()->create(['role' => 'student']);
        User::factory()->superAdmin()->create();

        $job = new CrisisNotificationJob('screening', 'high');
        $job->handle();

        Notification::assertNotSentTo($student, CrisisDetectedNotification::class);
    }

    public function test_crisis_notification_contains_correct_data(): void
    {
        $notification = new CrisisDetectedNotification('chat', 'high');

        $this->assertEquals('chat', $notification->source);
        $this->assertEquals('high', $notification->severity);
    }

    public function test_crisis_notification_database_payload(): void
    {
        $notification = new CrisisDetectedNotification('screening', 'high');

        $array = $notification->toArray(new User);

        $this->assertEquals('screening', $array['source']);
        $this->assertEquals('high', $array['severity']);
        $this->assertStringContainsString('screening', $array['message']);
    }

    public function test_crisis_notification_mail_content(): void
    {
        $notification = new CrisisDetectedNotification('chat', 'high');
        $user = User::factory()->superAdmin()->make();

        $mail = $notification->toMail($user);

        $this->assertEquals('Crisis Event Detected — Sanad', $mail->subject);
    }

    public function test_crisis_notification_uses_mail_and_database_channels(): void
    {
        $notification = new CrisisDetectedNotification('chat', 'high');
        $user = User::factory()->superAdmin()->make();

        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
        $this->assertContains('database', $channels);
    }
}
