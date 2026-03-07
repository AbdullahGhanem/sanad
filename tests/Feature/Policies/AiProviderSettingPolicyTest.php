<?php

namespace Tests\Feature\Policies;

use App\Models\AiProviderSetting;
use App\Models\User;
use App\Policies\AiProviderSettingPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiProviderSettingPolicyTest extends TestCase
{
    use RefreshDatabase;

    private AiProviderSettingPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AiProviderSettingPolicy;
    }

    public function test_super_admin_can_view_any_ai_settings(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertTrue($this->policy->viewAny($superAdmin));
    }

    public function test_university_admin_cannot_view_any_ai_settings(): void
    {
        $universityAdmin = User::factory()->universityAdmin()->create();

        $this->assertFalse($this->policy->viewAny($universityAdmin));
    }

    public function test_student_cannot_view_any_ai_settings(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->assertFalse($this->policy->viewAny($student));
    }

    public function test_super_admin_can_create_ai_setting(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertTrue($this->policy->create($superAdmin));
    }

    public function test_super_admin_can_update_ai_setting(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $setting = AiProviderSetting::factory()->create();

        $this->assertTrue($this->policy->update($superAdmin, $setting));
    }

    public function test_super_admin_can_delete_ai_setting(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $setting = AiProviderSetting::factory()->create();

        $this->assertTrue($this->policy->delete($superAdmin, $setting));
    }

    public function test_university_admin_cannot_create_ai_setting(): void
    {
        $universityAdmin = User::factory()->universityAdmin()->create();

        $this->assertFalse($this->policy->create($universityAdmin));
    }
}
