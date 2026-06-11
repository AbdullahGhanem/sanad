<?php

namespace Tests\Feature\Student;

use App\Livewire\Settings\Profile;
use App\Models\User;
use App\Notifications\ScreeningReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ScreeningReminderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function student(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'student',
            'reminder_enabled' => true,
            'last_screened_at' => now()->subDays(40),
            'last_reminded_at' => null,
        ], $attributes));
    }

    public function test_due_student_is_reminded_and_stamped(): void
    {
        Notification::fake();
        $student = $this->student();

        $this->artisan('app:send-screening-reminders')->assertSuccessful();

        Notification::assertSentTo($student, ScreeningReminderNotification::class);
        $this->assertNotNull($student->fresh()->last_reminded_at);
    }

    public function test_opted_out_student_is_not_reminded(): void
    {
        Notification::fake();
        $student = $this->student(['reminder_enabled' => false]);

        $this->artisan('app:send-screening-reminders');

        Notification::assertNotSentTo($student, ScreeningReminderNotification::class);
    }

    public function test_recently_screened_student_is_not_reminded(): void
    {
        Notification::fake();
        $student = $this->student(['last_screened_at' => now()->subDays(5)]);

        $this->artisan('app:send-screening-reminders');

        Notification::assertNotSentTo($student, ScreeningReminderNotification::class);
    }

    public function test_student_who_never_screened_is_not_reminded(): void
    {
        Notification::fake();
        $student = $this->student(['last_screened_at' => null]);

        $this->artisan('app:send-screening-reminders');

        Notification::assertNotSentTo($student, ScreeningReminderNotification::class);
    }

    public function test_cooldown_prevents_repeat_reminders(): void
    {
        Notification::fake();
        $student = $this->student(['last_reminded_at' => now()->subDays(5)]);

        $this->artisan('app:send-screening-reminders');

        Notification::assertNotSentTo($student, ScreeningReminderNotification::class);
    }

    public function test_unverified_email_is_not_reminded(): void
    {
        Notification::fake();
        $student = $this->student(['email_verified_at' => null]);

        $this->artisan('app:send-screening-reminders');

        Notification::assertNotSentTo($student, ScreeningReminderNotification::class);
    }

    public function test_student_can_opt_out_from_profile_settings(): void
    {
        $student = User::factory()->create(['role' => 'student', 'reminder_enabled' => true]);
        $this->actingAs($student);

        Livewire::test(Profile::class)
            ->set('reminderEnabled', false)
            ->call('updateProfileInformation');

        $this->assertFalse($student->fresh()->reminder_enabled);
    }
}
