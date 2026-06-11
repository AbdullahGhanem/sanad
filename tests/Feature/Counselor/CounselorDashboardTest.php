<?php

namespace Tests\Feature\Counselor;

use App\Filament\Widgets\CrisisQueue;
use App\Filament\Widgets\CrisisTriageStats;
use App\Models\CounselorNote;
use App\Models\CrisisEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Tests\TestCase;

class CounselorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_crisis_queue_shows_only_unresolved_events(): void
    {
        $this->actingAs(User::factory()->counselor()->create());

        $open = CrisisEvent::factory()->create(['status' => 'open']);
        $acknowledged = CrisisEvent::factory()->create(['status' => 'acknowledged']);
        $resolved = CrisisEvent::factory()->create(['status' => 'resolved']);

        Livewire::test(CrisisQueue::class)
            ->assertCanSeeTableRecords([$open, $acknowledged])
            ->assertCanNotSeeTableRecords([$resolved]);
    }

    public function test_counselor_can_acknowledge_from_the_queue_widget(): void
    {
        $counselor = User::factory()->counselor()->create();
        $this->actingAs($counselor);

        $event = CrisisEvent::factory()->create(['status' => 'open']);

        Livewire::test(CrisisQueue::class)
            ->callTableAction('acknowledge', $event);

        $this->assertSame('acknowledged', $event->fresh()->status->value);
        $this->assertSame($counselor->id, $event->fresh()->handled_by_id);
    }

    public function test_triage_stats_widget_renders_for_a_counselor(): void
    {
        $this->actingAs(User::factory()->counselor()->create());

        CrisisEvent::factory()->create(['status' => 'open']);

        Livewire::test(CrisisTriageStats::class)
            ->assertOk()
            ->assertSee(__('admin.crisis_open'));
    }

    public function test_notes_thread_renders_each_note_with_its_author(): void
    {
        $counselor = User::factory()->counselor()->create(['name' => 'Dr. Sara']);
        $event = CrisisEvent::factory()->create();
        CounselorNote::factory()->create([
            'crisis_event_id' => $event->id,
            'user_id' => $counselor->id,
            'body' => 'Reached out to the student by phone.',
        ]);

        $html = View::make('filament.crisis-notes-thread', [
            'notes' => $event->notes()->with('author')->get(),
        ])->render();

        $this->assertStringContainsString('Reached out to the student by phone.', $html);
        $this->assertStringContainsString('Dr. Sara', $html);
    }

    public function test_empty_notes_thread_shows_placeholder(): void
    {
        $html = View::make('filament.crisis-notes-thread', ['notes' => collect()])->render();

        $this->assertStringContainsString(__('admin.no_notes_yet'), $html);
    }

    public function test_note_can_be_added_from_the_queue_widget(): void
    {
        $counselor = User::factory()->counselor()->create();
        $this->actingAs($counselor);

        $event = CrisisEvent::factory()->create(['status' => 'open']);

        Livewire::test(CrisisQueue::class)
            ->callTableAction('notes', $event, ['body' => 'Followed up by phone.']);

        $this->assertDatabaseHas('counselor_notes', [
            'crisis_event_id' => $event->id,
            'user_id' => $counselor->id,
            'body' => 'Followed up by phone.',
        ]);
    }
}
