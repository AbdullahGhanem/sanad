<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CrisisNotificationJob;
use App\Models\CrisisEvent;
use App\Services\CrisisDetectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CrisisNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_crisis_notification_job_is_dispatched(): void
    {
        Queue::fake();

        $service = app(CrisisDetectionService::class);
        $service->logCrisisEvent('anonymous-123', 'chat');

        Queue::assertPushed(CrisisNotificationJob::class, function ($job) {
            return $job->source === 'chat' && $job->severity === 'high';
        });
    }

    public function test_crisis_notification_job_logs_warning(): void
    {
        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->with('Crisis event detected', \Mockery::on(function ($context) {
                return $context['source'] === 'screening'
                    && $context['severity'] === 'high';
            }))
            ->once();

        $job = new CrisisNotificationJob('screening', 'high');
        $job->handle();
    }

    public function test_crisis_event_created_on_log_crisis_event(): void
    {
        Queue::fake();

        $service = app(CrisisDetectionService::class);
        $event = $service->logCrisisEvent('anon-456', 'screening');

        $this->assertInstanceOf(CrisisEvent::class, $event);
        $this->assertDatabaseHas('crisis_events', [
            'anonymous_id' => 'anon-456',
            'source' => 'screening',
            'severity' => 'high',
        ]);
    }
}
