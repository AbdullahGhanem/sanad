<?php

namespace Tests\Feature\Models;

use App\Enums\CopingTheme;
use App\Models\CopingExercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CopingExerciseTest extends TestCase
{
    use RefreshDatabase;

    public function test_themes_are_cast_to_an_array(): void
    {
        $exercise = CopingExercise::factory()->forTheme(CopingTheme::Anxiety)->create();

        $this->assertSame([CopingTheme::Anxiety->value], $exercise->fresh()->themes);
    }

    public function test_for_theme_scope_matches_json_membership(): void
    {
        CopingExercise::factory()->create(['themes' => [CopingTheme::Anxiety->value, CopingTheme::Stress->value]]);
        CopingExercise::factory()->create(['themes' => [CopingTheme::Sadness->value]]);

        $this->assertSame(1, CopingExercise::query()->forTheme(CopingTheme::Stress->value)->count());
        $this->assertSame(0, CopingExercise::query()->forTheme(CopingTheme::Anger->value)->count());
    }

    public function test_active_scope_excludes_inactive(): void
    {
        CopingExercise::factory()->create(['is_active' => true]);
        CopingExercise::factory()->inactive()->create();

        $this->assertSame(1, CopingExercise::query()->active()->count());
    }

    public function test_addresses_theme_reflects_membership(): void
    {
        $exercise = CopingExercise::factory()->forTheme(CopingTheme::Loneliness)->create();

        $this->assertTrue($exercise->addressesTheme(CopingTheme::Loneliness));
        $this->assertFalse($exercise->addressesTheme(CopingTheme::Anger));
    }

    public function test_localized_getters_switch_by_language(): void
    {
        $exercise = CopingExercise::factory()->create([
            'title_en' => 'Breathing',
            'title_ar' => 'تنفّس',
            'steps_en' => 'Inhale',
            'steps_ar' => 'استنشق',
        ]);

        $this->assertSame('تنفّس', $exercise->getTitle('ar'));
        $this->assertSame('Breathing', $exercise->getTitle('en'));
        $this->assertSame('استنشق', $exercise->getSteps('ar'));
    }
}
