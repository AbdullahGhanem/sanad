<?php

namespace Tests\Feature;

use App\Settings\LegalSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_terms_page_renders(): void
    {
        $this->get(route('terms'))->assertOk();
    }

    public function test_privacy_page_renders(): void
    {
        $this->get(route('privacy'))->assertOk();
    }

    public function test_about_page_renders(): void
    {
        $this->get(route('about'))->assertOk();
    }

    public function test_terms_page_displays_arabic_content_by_default(): void
    {
        $settings = app(LegalSettings::class);
        $settings->terms_ar = 'شروط الاستخدام';
        $settings->save();

        $this->get(route('terms'))->assertSee('شروط الاستخدام');
    }

    public function test_terms_page_displays_english_when_locale_is_english(): void
    {
        $settings = app(LegalSettings::class);
        $settings->terms_en = 'Terms of use content';
        $settings->save();

        $this->withSession(['locale' => 'en'])
            ->get(route('terms'))
            ->assertSee('Terms of use content');
    }

    public function test_privacy_page_displays_content_based_on_locale(): void
    {
        $settings = app(LegalSettings::class);
        $settings->privacy_en = 'Privacy content EN';
        $settings->privacy_ar = 'سياسة الخصوصية';
        $settings->save();

        $this->get(route('privacy'))->assertSee('سياسة الخصوصية');

        $this->withSession(['locale' => 'en'])
            ->get(route('privacy'))
            ->assertSee('Privacy content EN');
    }

    public function test_about_page_displays_content_based_on_locale(): void
    {
        $settings = app(LegalSettings::class);
        $settings->about_en = 'About Sanad content';
        $settings->about_ar = 'عن سند';
        $settings->save();

        $this->get(route('about'))->assertSee('عن سند');

        $this->withSession(['locale' => 'en'])
            ->get(route('about'))
            ->assertSee('About Sanad content');
    }

    public function test_footer_contains_legal_page_links(): void
    {
        $this->get(route('home'))
            ->assertSee(route('terms'))
            ->assertSee(route('privacy'))
            ->assertSee(route('about'));
    }
}
