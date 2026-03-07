<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\AiProviderSettings\AiProviderSettingResource;
use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use App\Filament\Resources\ScreeningSessions\ScreeningSessionResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\AiProviderSetting;
use App\Models\Questionnaire;
use App\Models\ScreeningSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint5AdminResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $universityAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->universityAdmin = User::factory()->universityAdmin()->create();
    }

    public function test_user_list_page_renders_for_super_admin(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(UserResource::getUrl('index'))
            ->assertOk();
    }

    public function test_user_list_page_forbidden_for_university_admin(): void
    {
        $this->actingAs($this->universityAdmin)
            ->get(UserResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_user_create_page_renders_for_super_admin(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(UserResource::getUrl('create'))
            ->assertOk();
    }

    public function test_questionnaire_list_page_renders(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(QuestionnaireResource::getUrl('index'))
            ->assertOk();
    }

    public function test_questionnaire_create_page_renders(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(QuestionnaireResource::getUrl('create'))
            ->assertOk();
    }

    public function test_questionnaire_list_shows_records(): void
    {
        Questionnaire::factory()->count(2)->create();

        $this->actingAs($this->superAdmin)
            ->get(QuestionnaireResource::getUrl('index'))
            ->assertOk();
    }

    public function test_ai_provider_setting_list_page_renders_for_super_admin(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(AiProviderSettingResource::getUrl('index'))
            ->assertOk();
    }

    public function test_ai_provider_setting_list_forbidden_for_university_admin(): void
    {
        $this->actingAs($this->universityAdmin)
            ->get(AiProviderSettingResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_ai_provider_setting_create_page_renders(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(AiProviderSettingResource::getUrl('create'))
            ->assertOk();
    }

    public function test_ai_provider_setting_list_shows_records(): void
    {
        AiProviderSetting::factory()->count(2)->create();

        $this->actingAs($this->superAdmin)
            ->get(AiProviderSettingResource::getUrl('index'))
            ->assertOk();
    }

    public function test_screening_session_list_page_renders(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(ScreeningSessionResource::getUrl('index'))
            ->assertOk();
    }

    public function test_screening_session_cannot_be_created(): void
    {
        $this->assertFalse(ScreeningSessionResource::canCreate());
    }

    public function test_screening_session_list_shows_records(): void
    {
        ScreeningSession::factory()->count(3)->create();

        $this->actingAs($this->superAdmin)
            ->get(ScreeningSessionResource::getUrl('index'))
            ->assertOk();
    }

    public function test_student_cannot_access_user_resource(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(UserResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_student_cannot_access_ai_settings_resource(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(AiProviderSettingResource::getUrl('index'))
            ->assertForbidden();
    }
}
