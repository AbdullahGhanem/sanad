<?php

use App\Livewire\Screening\ScreeningWizard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('language/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('language.switch');

Route::view('terms', 'legal.terms')->name('terms');
Route::view('privacy', 'legal.privacy')->name('privacy');
Route::view('about', 'legal.about')->name('about');

Route::get('screening', ScreeningWizard::class)->name('screening');
Route::get('screening/results/{session}', \App\Livewire\Screening\ScreeningResults::class)->name('screening.results');
Route::get('chat/{session?}', \App\Livewire\Chat\ChatInterface::class)->name('chat');
Route::get('screening/results/{session}/pdf', [\App\Http\Controllers\ScreeningPdfController::class, 'download'])->name('screening.results.pdf');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
