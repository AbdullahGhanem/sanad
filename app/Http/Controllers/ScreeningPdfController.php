<?php

namespace App\Http\Controllers;

use App\Models\ScreeningSession;
use App\Services\CombinedScoringService;
use App\Services\Gad7ScoringService;
use App\Services\Phq9ScoringService;
use Barryvdh\DomPDF\Facade\Pdf;

class ScreeningPdfController extends Controller
{
    public function download(ScreeningSession $session): \Illuminate\Http\Response
    {
        $phq9Service = app(Phq9ScoringService::class);
        $gad7Service = app(Gad7ScoringService::class);
        $combinedService = app(CombinedScoringService::class);

        $pdf = Pdf::loadView('pdf.screening-results', [
            'session' => $session,
            'phq9Severity' => $phq9Service->getSeverity($session->phq9_score),
            'gad7Severity' => $gad7Service->getSeverity($session->gad7_score),
            'phq9Description' => $phq9Service->getSeverityDescription($session->phq9_score),
            'gad7Description' => $gad7Service->getSeverityDescription($session->gad7_score),
            'combinedDescription' => $combinedService->getCombinedDescription($session->phq9_score, $session->gad7_score),
        ]);

        return $pdf->download('sanad-screening-results.pdf');
    }
}
