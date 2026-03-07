<?php

namespace Tests\Feature\Screening;

use App\Livewire\Screening\ScreeningResults;
use App\Models\ScreeningSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ScreeningResultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_results_page_renders_for_valid_session(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 5,
            'gad7_score' => 3,
            'combined_severity' => 'mild',
            'completed_at' => now(),
        ]);

        $this->get(route('screening.results', $session))
            ->assertOk();
    }

    public function test_results_page_returns404_for_invalid_session(): void
    {
        $this->get(route('screening.results', 9999))
            ->assertNotFound();
    }

    public function test_results_displays_phq9_score(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 15,
            'gad7_score' => 10,
            'combined_severity' => 'moderately_severe',
            'completed_at' => now(),
        ]);

        Livewire::test(ScreeningResults::class, ['session' => $session])
            ->assertSee('15')
            ->assertSee('10');
    }

    public function test_results_displays_severity_label(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 0,
            'gad7_score' => 0,
            'combined_severity' => 'minimal',
            'completed_at' => now(),
        ]);

        Livewire::test(ScreeningResults::class, ['session' => $session])
            ->assertSee('بسيطة');
    }

    public function test_results_switches_language(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 0,
            'gad7_score' => 0,
            'combined_severity' => 'minimal',
            'completed_at' => now(),
        ]);

        Livewire::test(ScreeningResults::class, ['session' => $session])
            ->call('switchLanguage', 'ar')
            ->assertSet('language', 'ar')
            ->assertSee('بسيطة');
    }

    public function test_results_shows_severe_warning(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 25,
            'gad7_score' => 18,
            'combined_severity' => 'severe',
            'completed_at' => now(),
        ]);

        app()->setLocale('en');

        Livewire::test(ScreeningResults::class, ['session' => $session])
            ->assertSee('Severe')
            ->assertSee('severe depression symptoms');
    }
}
