<?php

namespace Tests\Feature\Student;

use App\Livewire\Student\MyProgress;
use App\Models\ScreeningSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/my-progress')->assertRedirect(route('login'));
    }

    public function test_empty_state_when_the_student_has_no_screenings(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']));

        Livewire::test(MyProgress::class)
            ->assertSee(__('progress.empty_title'));
    }

    public function test_shows_only_the_students_own_completed_screenings(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $other = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        ScreeningSession::factory()->completed()->create(['user_id' => $student->id, 'phq9_score' => 5]);
        ScreeningSession::factory()->completed()->create(['user_id' => $other->id, 'phq9_score' => 20]);
        ScreeningSession::factory()->create(['user_id' => $student->id, 'completed_at' => null]); // incomplete

        Livewire::test(MyProgress::class)
            ->assertSee('PHQ-9: 5')
            ->assertDontSee('PHQ-9: 20');
    }

    public function test_anonymous_sessions_are_claimed_on_visit(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        session(['guest_id' => 'guest-xyz']);

        $anonymous = ScreeningSession::factory()->completed()->create([
            'user_id' => null,
            'anonymous_id' => 'guest-xyz',
        ]);

        $this->actingAs($student);
        Livewire::test(MyProgress::class);

        $this->assertDatabaseHas('screening_sessions', [
            'id' => $anonymous->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_trend_reports_improvement_when_the_latest_score_is_lower(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        ScreeningSession::factory()->completed()->create([
            'user_id' => $student->id, 'phq9_score' => 14, 'completed_at' => now()->subWeek(),
        ]);
        ScreeningSession::factory()->completed()->create([
            'user_id' => $student->id, 'phq9_score' => 6, 'completed_at' => now(),
        ]);

        Livewire::test(MyProgress::class)
            ->assertSee(__('progress.improving'));
    }
}
