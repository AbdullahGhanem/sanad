<?php

namespace Tests\Feature\Counselor;

use App\Enums\CrisisStatus;
use App\Filament\Resources\AiProviderSettings\AiProviderSettingResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\CounselorNote;
use App\Models\CrisisEvent;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class CrisisTriageTest extends TestCase
{
    use RefreshDatabase;

    public function test_acknowledge_assigns_the_counselor_and_timestamp(): void
    {
        $counselor = User::factory()->counselor()->create();
        $event = CrisisEvent::factory()->create();

        $event->acknowledge($counselor);
        $event->refresh();

        $this->assertSame(CrisisStatus::Acknowledged, $event->status);
        $this->assertSame($counselor->id, $event->handled_by_id);
        $this->assertNotNull($event->handled_at);
    }

    public function test_resolve_marks_the_event_resolved(): void
    {
        $counselor = User::factory()->counselor()->create();
        $event = CrisisEvent::factory()->create();

        $event->resolve($counselor);

        $this->assertTrue($event->fresh()->isResolved());
    }

    public function test_unresolved_scope_excludes_resolved_events(): void
    {
        CrisisEvent::factory()->create(['status' => 'open']);
        CrisisEvent::factory()->create(['status' => 'acknowledged']);
        CrisisEvent::factory()->create(['status' => 'resolved']);

        $this->assertSame(2, CrisisEvent::query()->unresolved()->count());
    }

    public function test_notes_belong_to_the_event_and_record_their_author(): void
    {
        $event = CrisisEvent::factory()->create();
        $counselor = User::factory()->counselor()->create();

        $note = CounselorNote::factory()->create([
            'crisis_event_id' => $event->id,
            'user_id' => $counselor->id,
            'body' => 'Called the student and shared the hotline.',
        ]);

        $this->assertTrue($event->notes->contains($note));
        $this->assertSame($counselor->id, $note->author->id);
    }

    public function test_triage_actions_are_written_to_the_audit_trail(): void
    {
        $counselor = User::factory()->counselor()->create();
        $event = CrisisEvent::factory()->create();

        $event->acknowledge($counselor);

        $activity = Activity::where('log_name', 'crisis')
            ->where('subject_id', $event->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame('acknowledged', $activity->attribute_changes['attributes']['status']);
    }

    public function test_counselor_can_access_the_panel_but_not_admin_resources(): void
    {
        $counselor = User::factory()->counselor()->create();
        $this->actingAs($counselor);

        $this->assertTrue($counselor->canAccessPanel(Filament::getPanel('admin')));
        $this->assertFalse(UserResource::canAccess());
        $this->assertFalse(AiProviderSettingResource::canAccess());
    }

    public function test_admins_retain_access_to_admin_resources(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->actingAs($admin);

        $this->assertTrue(UserResource::canAccess());
        $this->assertTrue(AiProviderSettingResource::canAccess());
    }

    public function test_role_predicates(): void
    {
        $this->assertTrue(User::factory()->counselor()->create()->isCounselor());
        $this->assertFalse(User::factory()->counselor()->create()->isAdmin());
        $this->assertTrue(User::factory()->superAdmin()->create()->isAdmin());
        $this->assertFalse(User::factory()->create(['role' => 'student'])->canAccessPanel(Filament::getPanel('admin')));
    }
}
