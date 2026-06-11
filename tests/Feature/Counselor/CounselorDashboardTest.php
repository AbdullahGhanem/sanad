<?php

namespace Tests\Feature\Counselor;

use App\Filament\Widgets\CrisisQueue;
use App\Filament\Widgets\CrisisTriageStats;
use App\Models\CrisisEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
