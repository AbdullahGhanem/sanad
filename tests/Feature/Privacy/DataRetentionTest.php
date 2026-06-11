<?php

namespace Tests\Feature\Privacy;

use App\Models\ChatMessage;
use App\Models\CrisisEvent;
use App\Models\ScreeningSession;
use App\Models\SessionAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class DataRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_screening_sessions_past_retention_are_purged(): void
    {
        config(['retention.screening_sessions' => 30]);

        $old = ScreeningSession::factory()->create(['created_at' => now()->subDays(40)]);
        $recent = ScreeningSession::factory()->create(['created_at' => now()->subDays(10)]);

        $this->artisan('app:purge-expired-data')->assertSuccessful();

        $this->assertDatabaseMissing('screening_sessions', ['id' => $old->id]);
        $this->assertDatabaseHas('screening_sessions', ['id' => $recent->id]);
    }

    public function test_purging_a_session_cascades_to_answers_and_chat_messages(): void
    {
        config(['retention.screening_sessions' => 30, 'retention.chat_messages' => null]);

        $session = ScreeningSession::factory()->create(['created_at' => now()->subDays(40)]);
        $answer = SessionAnswer::factory()->create(['screening_session_id' => $session->id]);
        $message = ChatMessage::factory()->create([
            'screening_session_id' => $session->id,
            'created_at' => now(),
        ]);

        $this->artisan('app:purge-expired-data')->assertSuccessful();

        $this->assertDatabaseMissing('session_answers', ['id' => $answer->id]);
        $this->assertDatabaseMissing('chat_messages', ['id' => $message->id]);
    }

    public function test_chat_messages_past_retention_are_purged(): void
    {
        config(['retention.chat_messages' => 30, 'retention.screening_sessions' => null]);

        $old = ChatMessage::factory()->create(['screening_session_id' => null, 'created_at' => now()->subDays(40)]);
        $recent = ChatMessage::factory()->create(['screening_session_id' => null, 'created_at' => now()->subDays(5)]);

        $this->artisan('app:purge-expired-data')->assertSuccessful();

        $this->assertDatabaseMissing('chat_messages', ['id' => $old->id]);
        $this->assertDatabaseHas('chat_messages', ['id' => $recent->id]);
    }

    public function test_crisis_events_are_kept_when_retention_disabled(): void
    {
        config(['retention.crisis_events' => null]);

        $event = CrisisEvent::factory()->create(['created_at' => now()->subDays(1000)]);

        $this->artisan('app:purge-expired-data')->assertSuccessful();

        $this->assertDatabaseHas('crisis_events', ['id' => $event->id]);
    }

    public function test_purge_writes_an_audit_activity(): void
    {
        config(['retention.screening_sessions' => 30]);
        ScreeningSession::factory()->create(['created_at' => now()->subDays(40)]);

        $this->artisan('app:purge-expired-data')->assertSuccessful();

        $activity = Activity::where('log_name', 'retention')->latest()->first();

        $this->assertNotNull($activity);
        $this->assertSame('Purged expired data', $activity->description);
        $this->assertArrayHasKey('screening_sessions', $activity->properties['purged']);
    }

    public function test_dry_run_deletes_nothing(): void
    {
        config(['retention.screening_sessions' => 30]);
        $old = ScreeningSession::factory()->create(['created_at' => now()->subDays(40)]);

        $this->artisan('app:purge-expired-data', ['--dry-run' => true])->assertSuccessful();

        $this->assertDatabaseHas('screening_sessions', ['id' => $old->id]);
    }
}
