<?php

namespace Tests\Feature\Ai;

use App\Ai\Agents\SanadChat;
use App\Ai\Tools\GetCrisisResources;
use App\Models\CrisisHelpResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class GetCrisisResourcesToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_is_exposed_with_snake_case_name(): void
    {
        $this->assertSame('get_crisis_resources', (new GetCrisisResources)->name());
    }

    public function test_sanad_chat_registers_the_crisis_tool(): void
    {
        $tools = iterator_to_array((new SanadChat)->tools());

        $this->assertContainsOnlyInstancesOf(\Laravel\Ai\Contracts\Tool::class, $tools);
        $this->assertNotEmpty(array_filter(
            $tools,
            fn ($tool) => $tool instanceof GetCrisisResources,
        ));
    }

    public function test_handle_returns_active_resources_in_sort_order(): void
    {
        CrisisHelpResource::factory()->create([
            'type' => 'phone',
            'title_en' => 'Egypt Mental Health Hotline',
            'value' => '08008880700',
            'detail_en' => 'Free, confidential, 24/7',
            'url' => null,
            'sort_order' => 1,
        ]);
        CrisisHelpResource::factory()->create([
            'type' => 'website',
            'title_en' => 'Nefsy',
            'value' => 'nefsy.com',
            'detail_en' => null,
            'url' => 'https://nefsy.com',
            'sort_order' => 0,
        ]);

        $output = (string) (new GetCrisisResources('en'))->handle(new Request);

        $this->assertStringContainsString('Egypt Mental Health Hotline (phone): 08008880700 — Free, confidential, 24/7', $output);
        $this->assertStringContainsString('Nefsy (website): nefsy.com [https://nefsy.com]', $output);
        // Lower sort_order (Nefsy = 0) must come before the hotline (1).
        $this->assertLessThan(strpos($output, 'Egypt Mental Health Hotline'), strpos($output, 'Nefsy'));
    }

    public function test_handle_excludes_inactive_resources(): void
    {
        CrisisHelpResource::factory()->create([
            'title_en' => 'Active Line',
            'is_active' => true,
        ]);
        CrisisHelpResource::factory()->create([
            'title_en' => 'Retired Line',
            'is_active' => false,
        ]);

        $output = (string) (new GetCrisisResources('en'))->handle(new Request);

        $this->assertStringContainsString('Active Line', $output);
        $this->assertStringNotContainsString('Retired Line', $output);
    }

    public function test_handle_filters_by_type(): void
    {
        CrisisHelpResource::factory()->create(['type' => 'phone', 'title_en' => 'A Hotline']);
        CrisisHelpResource::factory()->create(['type' => 'website', 'title_en' => 'A Website']);

        $output = (string) (new GetCrisisResources('en'))->handle(new Request(['type' => 'phone']));

        $this->assertStringContainsString('A Hotline', $output);
        $this->assertStringNotContainsString('A Website', $output);
    }

    public function test_handle_returns_arabic_titles_when_language_is_arabic(): void
    {
        CrisisHelpResource::factory()->create([
            'title_en' => 'English Title',
            'title_ar' => 'عنوان عربي',
            'detail_en' => 'English detail',
            'detail_ar' => 'تفصيل عربي',
        ]);

        $output = (string) (new GetCrisisResources('ar'))->handle(new Request);

        $this->assertStringContainsString('عنوان عربي', $output);
        $this->assertStringNotContainsString('English Title', $output);
    }

    public function test_handle_returns_safe_fallback_when_no_resources_configured(): void
    {
        $english = (string) (new GetCrisisResources('en'))->handle(new Request);
        $arabic = (string) (new GetCrisisResources('ar'))->handle(new Request);

        $this->assertStringContainsString('emergency service', $english);
        $this->assertStringContainsString('طوارئ', $arabic);
    }
}
