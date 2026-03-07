<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_arabic(): void
    {
        $this->assertEquals('ar', config('app.locale'));
    }

    public function test_set_locale_middleware_defaults_to_arabic(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertEquals('ar', app()->getLocale());
    }

    public function test_set_locale_middleware_reads_from_session(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/');

        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_language_switcher_changes_locale_to_english(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\Livewire\Mechanisms\ComponentRegistry::class ? 'language-switcher' : 'language-switcher')
            ->call('switchLanguage', 'en');

        $this->assertEquals('en', session('locale'));
    }

    public function test_language_switcher_changes_locale_to_arabic(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('language-switcher')
            ->call('switchLanguage', 'ar');

        $this->assertEquals('ar', session('locale'));
    }

    public function test_language_switcher_rejects_invalid_locale(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('language-switcher')
            ->call('switchLanguage', 'fr');

        $this->assertNull(session('locale'));
    }

    public function test_header_shows_language_switcher_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('language-switcher');
    }
}
