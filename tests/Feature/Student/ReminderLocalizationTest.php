<?php

namespace Tests\Feature\Student;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use App\Notifications\ScreeningReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_preferred_locale_uses_the_stored_value_or_falls_back(): void
    {
        $this->assertSame('ar', User::factory()->create(['locale' => 'ar'])->preferredLocale());
        $this->assertSame(config('app.fallback_locale'), User::factory()->create(['locale' => null])->preferredLocale());
    }

    public function test_reminder_mail_is_translated(): void
    {
        $user = User::factory()->create(['name' => 'Sara']);

        app()->setLocale('ar');
        $arabic = (new ScreeningReminderNotification)->toMail($user);

        app()->setLocale('en');
        $english = (new ScreeningReminderNotification)->toMail($user);

        $this->assertSame('تذكير لطيف من سند', $arabic->subject);
        $this->assertNotSame($english->subject, $arabic->subject);
    }

    public function test_language_switch_persists_the_locale_for_an_authenticated_user(): void
    {
        $user = User::factory()->create(['locale' => null]);

        $this->actingAs($user)->get('/language/ar')->assertRedirect();

        $this->assertSame('ar', $user->fresh()->locale);
    }

    public function test_registration_captures_the_current_locale(): void
    {
        app()->setLocale('ar');

        $user = (new CreateNewUser)->create([
            'name' => 'New Student',
            'email' => 'new@student.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertSame('ar', $user->locale);
    }
}
