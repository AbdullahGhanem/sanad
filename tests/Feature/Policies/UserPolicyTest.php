<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy;
    }

    public function test_super_admin_can_view_any_users(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertTrue($this->policy->viewAny($superAdmin));
    }

    public function test_university_admin_cannot_view_any_users(): void
    {
        $universityAdmin = User::factory()->universityAdmin()->create();

        $this->assertFalse($this->policy->viewAny($universityAdmin));
    }

    public function test_student_cannot_view_any_users(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->assertFalse($this->policy->viewAny($student));
    }

    public function test_super_admin_can_create_users(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertTrue($this->policy->create($superAdmin));
    }

    public function test_super_admin_can_update_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $target = User::factory()->create();

        $this->assertTrue($this->policy->update($superAdmin, $target));
    }

    public function test_super_admin_can_delete_other_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $target = User::factory()->create();

        $this->assertTrue($this->policy->delete($superAdmin, $target));
    }

    public function test_super_admin_cannot_delete_self(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertFalse($this->policy->delete($superAdmin, $superAdmin));
    }

    public function test_super_admin_cannot_force_delete_self(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertFalse($this->policy->forceDelete($superAdmin, $superAdmin));
    }

    public function test_super_admin_can_restore_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $target = User::factory()->create();

        $this->assertTrue($this->policy->restore($superAdmin, $target));
    }
}
