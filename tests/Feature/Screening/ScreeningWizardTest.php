<?php

namespace Tests\Feature\Screening;

use App\Livewire\Screening\ScreeningWizard;
use Database\Seeders\Gad7Seeder;
use Database\Seeders\Phq9Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ScreeningWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([Phq9Seeder::class, Gad7Seeder::class]);
    }

    public function test_screening_page_renders(): void
    {
        $this->get(route('screening'))
            ->assertOk();
    }

    public function test_screening_defaults_to_app_locale(): void
    {
        Livewire::test(ScreeningWizard::class)
            ->assertSet('language', 'ar')
            ->assertSet('currentStep', 0)
            ->assertSet('phase', 'phq9');
    }

    public function test_screening_initializes_with_correct_total_steps(): void
    {
        app()->setLocale('en');

        Livewire::test(ScreeningWizard::class)
            ->assertSet('currentStep', 0)
            ->assertSee('Question 1 / 16');
    }

    public function test_screening_switches_language(): void
    {
        Livewire::test(ScreeningWizard::class)
            ->call('switchLanguage', 'ar')
            ->assertSet('language', 'ar')
            ->call('switchLanguage', 'en')
            ->assertSet('language', 'en');
    }

    public function test_screening_rejects_invalid_language(): void
    {
        Livewire::test(ScreeningWizard::class)
            ->call('switchLanguage', 'en')
            ->call('switchLanguage', 'fr')
            ->assertSet('language', 'en');
    }

    public function test_select_option_advances_to_next_step(): void
    {
        Livewire::test(ScreeningWizard::class)
            ->call('selectOption', 0)
            ->assertSet('currentStep', 1);
    }

    public function test_previous_goes_back_one_step(): void
    {
        Livewire::test(ScreeningWizard::class)
            ->call('selectOption', 0) // step 0 → 1
            ->call('previous')
            ->assertSet('currentStep', 0);
    }

    public function test_previous_does_nothing_at_step_zero(): void
    {
        Livewire::test(ScreeningWizard::class)
            ->call('previous')
            ->assertSet('currentStep', 0);
    }

    public function test_phase_transitions_from_phq9_to_gad7(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        // Answer all 9 PHQ-9 questions
        for ($i = 0; $i < 9; $i++) {
            $component->call('selectOption', 0);
        }

        $component->assertSet('phase', 'gad7')
            ->assertSet('currentStep', 9);
    }

    public function test_crisis_overlay_shows_on_item9_high_score(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        // Answer first 8 PHQ-9 questions
        for ($i = 0; $i < 8; $i++) {
            $component->call('selectOption', 0);
        }

        // Item 9 with score >= 2 triggers crisis
        $component->assertSet('currentStep', 8)
            ->call('selectOption', 2)
            ->assertSet('showCrisisOverlay', true);
    }

    public function test_crisis_overlay_does_not_show_on_item9_low_score(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        for ($i = 0; $i < 8; $i++) {
            $component->call('selectOption', 0);
        }

        $component->call('selectOption', 1)
            ->assertSet('showCrisisOverlay', false);
    }

    public function test_acknowledge_crisis_dismisses_overlay_and_advances(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        for ($i = 0; $i < 8; $i++) {
            $component->call('selectOption', 0);
        }

        $component->call('selectOption', 2)
            ->assertSet('showCrisisOverlay', true)
            ->call('acknowledgeCrisis')
            ->assertSet('showCrisisOverlay', false)
            ->assertSet('crisisAcknowledged', true)
            ->assertSet('currentStep', 9);
    }

    public function test_crisis_event_is_logged_on_acknowledge(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        for ($i = 0; $i < 8; $i++) {
            $component->call('selectOption', 0);
        }

        $component->call('selectOption', 2)
            ->call('acknowledgeCrisis');

        $this->assertDatabaseHas('crisis_events', [
            'source' => 'screening',
            'severity' => 'high',
        ]);
    }

    public function test_questions_transition_to_free_text_phase(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        for ($i = 0; $i < 16; $i++) {
            $component->call('selectOption', 0);
        }

        $component->assertSet('phase', 'free_text')
            ->assertSet('isCompleted', false);
    }

    public function test_completing_screening_creates_session(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        for ($i = 0; $i < 16; $i++) {
            $component->call('selectOption', 0);
        }

        $component->assertSet('phase', 'free_text')
            ->call('skipFreeText')
            ->assertSet('isCompleted', true);

        $this->assertDatabaseHas('screening_sessions', [
            'phq9_score' => 0,
            'gad7_score' => 0,
            'combined_severity' => 'minimal',
        ]);
    }

    public function test_completing_screening_saves_all_answers(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        for ($i = 0; $i < 16; $i++) {
            $component->call('selectOption', 1);
        }

        $component->call('skipFreeText')
            ->assertSet('isCompleted', true);

        $this->assertDatabaseHas('screening_sessions', [
            'phq9_score' => 9,
            'gad7_score' => 7,
        ]);

        $this->assertDatabaseCount('session_answers', 16);
    }

    public function test_completing_screening_calculates_correct_scores(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        // PHQ-9: first 8 questions with score 3
        for ($i = 0; $i < 8; $i++) {
            $component->call('selectOption', 3);
        }

        // Item 9 (crisis trigger) with score 3
        $component->call('selectOption', 3);
        $component->assertSet('showCrisisOverlay', true);
        $component->call('acknowledgeCrisis');

        // GAD-7: all 7 questions with score 3
        for ($i = 0; $i < 7; $i++) {
            $component->call('selectOption', 3);
        }

        $component->assertSet('phase', 'free_text')
            ->call('skipFreeText')
            ->assertSet('isCompleted', true);

        $this->assertDatabaseHas('screening_sessions', [
            'phq9_score' => 27,
            'gad7_score' => 21,
            'combined_severity' => 'severe',
        ]);
    }

    public function test_next_does_nothing_when_no_option_selected(): void
    {
        Livewire::test(ScreeningWizard::class)
            ->call('next')
            ->assertSet('currentStep', 0);
    }

    public function test_progress_percentage_calculates_correctly(): void
    {
        $component = Livewire::test(ScreeningWizard::class);

        // At step 0: 0/16 = 0%
        $this->assertSame(0, $component->get('progressPercentage'));

        $component->call('selectOption', 0);
        // At step 1: 1/16 = 6%
        $this->assertSame(6, $component->get('progressPercentage'));
    }
}
