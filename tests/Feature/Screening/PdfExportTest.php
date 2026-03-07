<?php

namespace Tests\Feature\Screening;

use App\Models\ScreeningSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_download_returns_ok(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 10,
            'gad7_score' => 8,
            'combined_severity' => 'moderate',
        ]);

        $response = $this->get(route('screening.results.pdf', $session));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_download_contains_filename(): void
    {
        $session = ScreeningSession::factory()->create([
            'phq9_score' => 5,
            'gad7_score' => 3,
            'combined_severity' => 'mild',
        ]);

        $response = $this->get(route('screening.results.pdf', $session));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('sanad-screening-results.pdf', $response->headers->get('content-disposition'));
    }

    public function test_pdf_download_returns404_for_invalid_session(): void
    {
        $this->get(route('screening.results.pdf', ['session' => 99999]))
            ->assertNotFound();
    }
}
