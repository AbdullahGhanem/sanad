<?php

namespace Tests\Feature\Crisis;

use App\Events\CrisisDetected;
use App\Jobs\CrisisNotificationJob;
use App\Listeners\EscalateCrisis;
use App\Livewire\Screening\ScreeningWizard;
use App\Models\CrisisEvent;
use App\Models\User;
use App\Notifications\CrisisDetectedNotification;
use App\Services\CrisisDetectionService;
use Database\Seeders\Gad7Seeder;
use Database\Seeders\Phq9Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class CrisisEscalationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([Phq9Seeder::class, Gad7Seeder::class]);
    }

    public function test_logging_a_crisis_dispatches_the_crisis_detected_event(): void
    {
        Event::fake([CrisisDetected::class]);

        app(CrisisDetectionService::class)->logCrisisEvent('anon-1', 'chat', 'en');

        Event::assertDispatched(
            CrisisDetected::class,
            fn (CrisisDetected $event): bool => $event->source() === 'chat'
                && $event->severity() === 'high'
                && $event->language === 'en',
        );
    }

    public function test_escalate_listener_dispatches_the_notification_job(): void
    {
        Queue::fake();
        $crisisEvent = CrisisEvent::factory()->create(['source' => 'chat', 'severity' => 'high']);

        (new EscalateCrisis)->handle(new CrisisDetected($crisisEvent));

        Queue::assertPushed(
            CrisisNotificationJob::class,
            fn (CrisisNotificationJob $job): bool => $job->source === 'chat' && $job->severity === 'high',
        );
    }

    public function test_crisis_pipeline_dispatches_notification_job_end_to_end(): void
    {
        Queue::fake();

        app(CrisisDetectionService::class)->logCrisisEvent('anon-2', 'chat');

        Queue::assertPushed(CrisisNotificationJob::class);
    }

    public function test_notification_includes_realtime_broadcast_channel(): void
    {
        $channels = (new CrisisDetectedNotification('chat', 'high'))->via(new User);

        $this->assertContains('broadcast', $channels);
        $this->assertContains('mail', $channels);
        $this->assertContains('database', $channels);
    }

    public function test_notification_builds_a_broadcast_message(): void
    {
        $message = (new CrisisDetectedNotification('chat', 'high'))->toBroadcast(new User);

        $this->assertInstanceOf(BroadcastMessage::class, $message);
        $this->assertNotEmpty($message->data);
    }

    public function test_screening_crisis_routes_through_the_escalation_pipeline(): void
    {
        Event::fake([CrisisDetected::class]);

        $component = Livewire::test(ScreeningWizard::class);

        for ($i = 0; $i < 8; $i++) {
            $component->call('selectOption', 0);
        }

        $component->call('selectOption', 2)
            ->call('acknowledgeCrisis');

        Event::assertDispatched(
            CrisisDetected::class,
            fn (CrisisDetected $event): bool => $event->source() === 'screening',
        );

        $this->assertDatabaseHas('crisis_events', [
            'source' => 'screening',
            'severity' => 'high',
        ]);
    }
}
