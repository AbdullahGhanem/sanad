<?php

namespace Tests\Feature\Counselor;

use App\Enums\ReferralStatus;
use App\Filament\Resources\Referrals\Pages\ListReferrals;
use App\Filament\Resources\Referrals\ReferralResource;
use App\Filament\Widgets\CrisisQueue;
use App\Models\CrisisEvent;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_sets_status_and_appointment_time(): void
    {
        $referral = Referral::factory()->create(['status' => 'pending', 'scheduled_at' => null]);

        $at = now()->addDays(2);
        $referral->schedule($at);
        $referral->refresh();

        $this->assertSame(ReferralStatus::Scheduled, $referral->status);
        $this->assertSame($at->toDateTimeString(), $referral->scheduled_at->toDateTimeString());
    }

    public function test_complete_and_decline_transition_status(): void
    {
        $a = Referral::factory()->create(['status' => 'scheduled']);
        $a->complete();
        $this->assertSame(ReferralStatus::Completed, $a->fresh()->status);

        $b = Referral::factory()->create(['status' => 'pending']);
        $b->decline();
        $this->assertSame(ReferralStatus::Declined, $b->fresh()->status);
    }

    public function test_open_scope_excludes_completed_and_declined(): void
    {
        Referral::factory()->create(['status' => 'pending']);
        Referral::factory()->create(['status' => 'scheduled']);
        Referral::factory()->create(['status' => 'completed']);
        Referral::factory()->create(['status' => 'declined']);

        $this->assertSame(2, Referral::query()->open()->count());
    }

    public function test_counselor_can_refer_a_crisis_event_from_the_queue(): void
    {
        $counselor = User::factory()->counselor()->create();
        $this->actingAs($counselor);

        $event = CrisisEvent::factory()->create(['status' => 'open', 'anonymous_id' => 'guest-7']);

        Livewire::test(CrisisQueue::class)
            ->callTableAction('refer', $event, ['reason' => 'High suicide risk, needs in-person support.']);

        $this->assertDatabaseHas('referrals', [
            'crisis_event_id' => $event->id,
            'anonymous_id' => 'guest-7',
            'referred_by_id' => $counselor->id,
            'status' => 'pending',
        ]);
    }

    public function test_refer_action_is_hidden_once_a_referral_exists(): void
    {
        $this->actingAs(User::factory()->counselor()->create());

        $event = CrisisEvent::factory()->create(['status' => 'open']);
        Referral::factory()->create(['crisis_event_id' => $event->id]);

        $this->assertTrue($event->hasReferral());

        Livewire::test(CrisisQueue::class)
            ->assertTableActionHidden('refer', $event);
    }

    public function test_referrals_are_audited(): void
    {
        $referral = Referral::factory()->create(['status' => 'pending']);
        $referral->complete();

        $activity = Activity::where('log_name', 'referral')
            ->where('subject_id', $referral->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame('completed', $activity->attribute_changes['attributes']['status']);
    }

    public function test_referral_resource_is_list_only(): void
    {
        $this->assertFalse(ReferralResource::canCreate());
        $this->assertSame(['index'], array_keys(ReferralResource::getPages()));
    }

    public function test_appointment_can_be_scheduled_from_the_resource(): void
    {
        $this->actingAs(User::factory()->counselor()->create());
        $referral = Referral::factory()->create(['status' => 'pending']);

        $at = now()->addDay()->startOfMinute();

        Livewire::test(ListReferrals::class)
            ->callTableAction('schedule', $referral, ['scheduled_at' => $at->format('Y-m-d H:i:s')]);

        $this->assertSame(ReferralStatus::Scheduled, $referral->fresh()->status);
        $this->assertNotNull($referral->fresh()->scheduled_at);
    }
}
