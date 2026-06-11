<?php

namespace Tests\Feature\Student;

use App\Livewire\Student\MyProgress;
use App\Models\Referral;
use App\Models\User;
use App\Notifications\ReferralCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class RequestCounselorTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_request_a_counselor(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Livewire::test(MyProgress::class)->call('requestCounselor');

        $this->assertDatabaseHas('referrals', [
            'user_id' => $student->id,
            'referred_by_id' => null,
            'status' => 'pending',
        ]);
    }

    public function test_optional_message_is_saved_as_the_referral_reason(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Livewire::test(MyProgress::class)
            ->set('requestMessage', "I've been struggling to sleep and would like to talk.")
            ->call('requestCounselor');

        $this->assertDatabaseHas('referrals', [
            'user_id' => $student->id,
            'reason' => "I've been struggling to sleep and would like to talk.",
        ]);
    }

    public function test_empty_message_falls_back_to_a_default_reason(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Livewire::test(MyProgress::class)
            ->set('requestMessage', '   ')
            ->call('requestCounselor');

        $this->assertDatabaseHas('referrals', [
            'user_id' => $student->id,
            'reason' => 'Student-requested counselor support.',
        ]);
    }

    public function test_overlong_message_is_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']));

        Livewire::test(MyProgress::class)
            ->set('requestMessage', str_repeat('a', 1001))
            ->call('requestCounselor')
            ->assertHasErrors('requestMessage');

        $this->assertSame(0, \App\Models\Referral::query()->count());
    }

    public function test_request_is_not_duplicated_while_one_is_open(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Livewire::test(MyProgress::class)
            ->call('requestCounselor')
            ->call('requestCounselor');

        $this->assertSame(1, Referral::query()->where('user_id', $student->id)->count());
    }

    public function test_pending_state_is_shown_after_requesting(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']));

        Livewire::test(MyProgress::class)
            ->assertSee(__('progress.request_counselor'))
            ->call('requestCounselor')
            ->assertSee(__('progress.request_pending'));
    }

    public function test_the_counseling_center_is_notified_on_a_self_request(): void
    {
        Notification::fake();
        $counselor = User::factory()->counselor()->create();
        $this->actingAs(User::factory()->create(['role' => 'student']));

        Livewire::test(MyProgress::class)->call('requestCounselor');

        Notification::assertSentTo($counselor, ReferralCreatedNotification::class);
    }

    public function test_can_request_again_after_the_previous_request_is_resolved(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        Referral::factory()->create([
            'user_id' => $student->id,
            'referred_by_id' => null,
            'status' => 'completed',
        ]);

        Livewire::test(MyProgress::class)->call('requestCounselor');

        $this->assertSame(1, Referral::query()->where('user_id', $student->id)->open()->count());
    }
}
