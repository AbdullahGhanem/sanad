<?php

namespace Tests\Feature\Ai;

use App\Ai\Tools\SuggestCopingExercise;
use App\Enums\CopingTheme;
use App\Models\CopingExercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class SuggestCopingExerciseToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_is_exposed_with_snake_case_name(): void
    {
        $this->assertSame('suggest_coping_exercise', (new SuggestCopingExercise)->name());
    }

    public function test_schema_exposes_a_theme_parameter(): void
    {
        $schema = (new SuggestCopingExercise)->schema(new JsonSchemaTypeFactory);

        $this->assertArrayHasKey('theme', $schema);
    }

    public function test_handle_returns_exercises_matching_the_theme(): void
    {
        CopingExercise::factory()->forTheme(CopingTheme::Anxiety)->create([
            'title_en' => 'Box Breathing',
            'steps_en' => 'Breathe in a square pattern.',
            'sort_order' => 0,
        ]);
        CopingExercise::factory()->forTheme(CopingTheme::Sadness)->create([
            'title_en' => 'Gratitude List',
        ]);

        $output = (string) (new SuggestCopingExercise('en'))->handle(new Request(['theme' => 'anxiety']));

        $this->assertStringContainsString('Box Breathing', $output);
        $this->assertStringContainsString('Breathe in a square pattern.', $output);
        $this->assertStringNotContainsString('Gratitude List', $output);
    }

    public function test_handle_excludes_inactive_exercises(): void
    {
        CopingExercise::factory()->forTheme(CopingTheme::Anxiety)->create(['title_en' => 'Active Exercise']);
        CopingExercise::factory()->forTheme(CopingTheme::Anxiety)->inactive()->create(['title_en' => 'Retired Exercise']);

        $output = (string) (new SuggestCopingExercise('en'))->handle(new Request(['theme' => 'anxiety']));

        $this->assertStringContainsString('Active Exercise', $output);
        $this->assertStringNotContainsString('Retired Exercise', $output);
    }

    public function test_handle_limits_results_to_three(): void
    {
        CopingExercise::factory()->count(5)->forTheme(CopingTheme::Anxiety)->create();

        $output = (string) (new SuggestCopingExercise('en'))->handle(new Request(['theme' => 'anxiety']));

        // Exercises are separated by a blank line; three exercises => two separators.
        $this->assertSame(2, substr_count($output, "\n\n"));
    }

    public function test_handle_falls_back_to_general_when_theme_has_no_match(): void
    {
        CopingExercise::factory()->forTheme(CopingTheme::General)->create(['title_en' => 'General Exercise']);

        $output = (string) (new SuggestCopingExercise('en'))->handle(new Request(['theme' => 'anger']));

        $this->assertStringContainsString('General Exercise', $output);
    }

    public function test_handle_falls_back_to_any_active_when_no_theme_given(): void
    {
        CopingExercise::factory()->forTheme(CopingTheme::Sadness)->create(['title_en' => 'Any Exercise']);

        $output = (string) (new SuggestCopingExercise('en'))->handle(new Request);

        $this->assertStringContainsString('Any Exercise', $output);
    }

    public function test_handle_ignores_unknown_themes_and_still_suggests(): void
    {
        CopingExercise::factory()->forTheme(CopingTheme::General)->create(['title_en' => 'Fallback Exercise']);

        $output = (string) (new SuggestCopingExercise('en'))->handle(new Request(['theme' => 'not-a-real-theme']));

        $this->assertStringContainsString('Fallback Exercise', $output);
    }

    public function test_handle_returns_arabic_content(): void
    {
        CopingExercise::factory()->forTheme(CopingTheme::Anxiety)->create([
            'title_en' => 'English Title',
            'title_ar' => 'عنوان عربي',
            'steps_en' => 'English steps',
            'steps_ar' => 'خطوات عربية',
        ]);

        $output = (string) (new SuggestCopingExercise('ar'))->handle(new Request(['theme' => 'anxiety']));

        $this->assertStringContainsString('عنوان عربي', $output);
        $this->assertStringContainsString('خطوات عربية', $output);
        $this->assertStringNotContainsString('English Title', $output);
    }

    public function test_handle_returns_fallback_message_when_no_exercises_configured(): void
    {
        $english = (string) (new SuggestCopingExercise('en'))->handle(new Request(['theme' => 'anxiety']));
        $arabic = (string) (new SuggestCopingExercise('ar'))->handle(new Request(['theme' => 'anxiety']));

        $this->assertStringContainsString('No exercises are currently configured', $english);
        $this->assertStringContainsString('لا توجد تمارين', $arabic);
    }
}
