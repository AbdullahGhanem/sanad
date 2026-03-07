<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\CrisisHelpResources\CrisisHelpResourceResource;
use App\Models\CrisisHelpResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrisisHelpResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
    }

    public function test_list_page_renders_for_admin(): void
    {
        CrisisHelpResource::factory()->count(3)->create();

        $this->actingAs($this->superAdmin)
            ->get(CrisisHelpResourceResource::getUrl('index'))
            ->assertOk();
    }

    public function test_create_page_renders_for_admin(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(CrisisHelpResourceResource::getUrl('create'))
            ->assertOk();
    }

    public function test_edit_page_renders_for_admin(): void
    {
        $resource = CrisisHelpResource::factory()->create();

        $this->actingAs($this->superAdmin)
            ->get(CrisisHelpResourceResource::getUrl('edit', ['record' => $resource]))
            ->assertOk();
    }

    public function test_model_scopes_work(): void
    {
        CrisisHelpResource::factory()->create(['is_active' => true, 'sort_order' => 2]);
        CrisisHelpResource::factory()->create(['is_active' => false, 'sort_order' => 1]);
        CrisisHelpResource::factory()->create(['is_active' => true, 'sort_order' => 0]);

        $active = CrisisHelpResource::active()->ordered()->get();

        $this->assertCount(2, $active);
        $this->assertEquals(0, $active->first()->sort_order);
    }

    public function test_get_title_returns_correct_language(): void
    {
        $resource = CrisisHelpResource::factory()->create([
            'title_en' => 'Hotline',
            'title_ar' => 'خط نجدة',
        ]);

        $this->assertEquals('Hotline', $resource->getTitle('en'));
        $this->assertEquals('خط نجدة', $resource->getTitle('ar'));
    }

    public function test_get_detail_returns_correct_language(): void
    {
        $resource = CrisisHelpResource::factory()->create([
            'detail_en' => 'Contact us',
            'detail_ar' => 'تواصل معنا',
        ]);

        $this->assertEquals('Contact us', $resource->getDetail('en'));
        $this->assertEquals('تواصل معنا', $resource->getDetail('ar'));
    }

    public function test_hotline_number_returns_active_phone_resource(): void
    {
        CrisisHelpResource::factory()->create([
            'type' => 'phone',
            'value' => '01012221',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertEquals('01012221', CrisisHelpResource::hotlineNumber());
    }

    public function test_hotline_number_returns_empty_string_when_no_phone(): void
    {
        CrisisHelpResource::factory()->create([
            'type' => 'website',
            'value' => 'https://example.com',
            'is_active' => true,
        ]);

        $this->assertEquals('', CrisisHelpResource::hotlineNumber());
    }
}
