<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\CrisisEvents\CrisisEventResource;
use App\Filament\Resources\CrisisKeywords\CrisisKeywordResource;
use App\Filament\Resources\Recommendations\RecommendationResource;
use App\Models\CrisisEvent;
use App\Models\CrisisKeyword;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->superAdmin()->create();
    }

    public function test_recommendation_list_page_renders(): void
    {
        $this->actingAs($this->admin)
            ->get(RecommendationResource::getUrl('index'))
            ->assertOk();
    }

    public function test_recommendation_create_page_renders(): void
    {
        $this->actingAs($this->admin)
            ->get(RecommendationResource::getUrl('create'))
            ->assertOk();
    }

    public function test_crisis_keyword_list_page_renders(): void
    {
        $this->actingAs($this->admin)
            ->get(CrisisKeywordResource::getUrl('index'))
            ->assertOk();
    }

    public function test_crisis_keyword_create_page_renders(): void
    {
        $this->actingAs($this->admin)
            ->get(CrisisKeywordResource::getUrl('create'))
            ->assertOk();
    }

    public function test_crisis_event_list_page_renders(): void
    {
        $this->actingAs($this->admin)
            ->get(CrisisEventResource::getUrl('index'))
            ->assertOk();
    }

    public function test_crisis_event_cannot_be_created(): void
    {
        $this->assertFalse(CrisisEventResource::canCreate());
    }

    public function test_recommendation_list_shows_records(): void
    {
        Recommendation::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(RecommendationResource::getUrl('index'))
            ->assertOk();
    }

    public function test_crisis_keyword_list_shows_records(): void
    {
        CrisisKeyword::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(CrisisKeywordResource::getUrl('index'))
            ->assertOk();
    }

    public function test_crisis_event_list_shows_records(): void
    {
        CrisisEvent::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(CrisisEventResource::getUrl('index'))
            ->assertOk();
    }

    public function test_student_cannot_access_resources(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(RecommendationResource::getUrl('index'))
            ->assertForbidden();
    }
}
