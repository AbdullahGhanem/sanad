<?php

namespace Tests\Feature\Privacy;

use App\Livewire\Screening\ScreeningWizard;
use App\Models\ConsentRecord;
use Database\Seeders\Gad7Seeder;
use Database\Seeders\Phq9Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConsentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([Phq9Seeder::class, Gad7Seeder::class]);
    }

    public function test_wizard_requires_consent_when_none_given(): void
    {
        session(['guest_id' => 'guest-1']);

        Livewire::test(ScreeningWizard::class)
            ->assertSet('needsConsent', true);
    }

    public function test_giving_consent_records_it_and_unlocks_the_wizard(): void
    {
        session(['guest_id' => 'guest-1']);

        Livewire::test(ScreeningWizard::class)
            ->call('giveConsent')
            ->assertSet('needsConsent', false);

        $this->assertDatabaseHas('consent_records', [
            'anonymous_id' => 'guest-1',
            'version' => '1.0',
        ]);
    }

    public function test_existing_consent_for_current_version_skips_the_gate(): void
    {
        session(['guest_id' => 'guest-1']);
        ConsentRecord::factory()->create(['anonymous_id' => 'guest-1', 'version' => config('consent.version')]);

        Livewire::test(ScreeningWizard::class)
            ->assertSet('needsConsent', false);
    }

    public function test_consent_for_an_older_version_requires_reconsent(): void
    {
        session(['guest_id' => 'guest-1']);
        ConsentRecord::factory()->create(['anonymous_id' => 'guest-1', 'version' => '0.9']);

        Livewire::test(ScreeningWizard::class)
            ->assertSet('needsConsent', true);
    }

    public function test_missing_guest_id_does_not_gate(): void
    {
        Livewire::test(ScreeningWizard::class)
            ->assertSet('needsConsent', false);
    }

    public function test_has_consented_helper(): void
    {
        ConsentRecord::record('g1', '1.0', 'en');

        $this->assertTrue(ConsentRecord::hasConsented('g1', '1.0'));
        $this->assertFalse(ConsentRecord::hasConsented('g1', '2.0'));
        $this->assertFalse(ConsentRecord::hasConsented('', '1.0'));
    }
}
