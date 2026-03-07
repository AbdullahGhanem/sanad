<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
    }

    public function test_super_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_university_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->universityAdmin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_student_cannot_access_admin_panel(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->get('/admin');

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_is_redirected_from_admin(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect();
    }

    public function test_user_model_has_correct_role_helpers(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $universityAdmin = User::factory()->universityAdmin()->create();
        $student = User::factory()->create();

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertFalse($superAdmin->isUniversityAdmin());

        $this->assertTrue($universityAdmin->isUniversityAdmin());
        $this->assertFalse($universityAdmin->isSuperAdmin());

        $this->assertFalse($student->isSuperAdmin());
        $this->assertFalse($student->isUniversityAdmin());
    }

    public function test_soft_deleted_user_cannot_login(): void
    {
        $user = User::factory()->superAdmin()->create();
        $user->delete();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }
}
