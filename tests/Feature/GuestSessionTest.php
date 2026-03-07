<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_homepage_without_login(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_guest_session_generates_anonymous_uuid(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSessionHas('guest_id');

        $guestId = session('guest_id');
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $guestId
        );
    }

    public function test_guest_session_id_persists_across_requests(): void
    {
        $response1 = $this->get('/');
        $guestId1 = session('guest_id');

        $response2 = $this->get('/');
        $guestId2 = session('guest_id');

        $this->assertEquals($guestId1, $guestId2);
    }

    public function test_authenticated_user_does_not_get_guest_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $response->assertSessionMissing('guest_id');
    }

    public function test_guest_cannot_access_admin_panel(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect();
    }
}
