<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentimentController;
use App\Http\Controllers\ScrapeController;
use App\Http\Controllers\KlipingController;
use App\Http\Controllers\KlipingsController;

// Halaman dashboard utama
Route::get('/', function () {
    return view('dashboard');
})->name('/');

// -------------------
// Analisis Sentimen
// -------------------
Route::get('/sentiment', [SentimentController::class, 'index'])->name('sentiment.form');
Route::post('/sentiment/analyze', [SentimentController::class, 'analyze'])->name('sentiment.analyze');

// -------------------
// Scrapping Berita
// -------------------
Route::get('/scrapping', [ScrapeController::class, 'showForm'])->name('scrapping.formScrapping');
Route::post('/scrapping', [ScrapeController::class, 'scrape'])->name('scrapping.result');
Route::get('/scrapping/results', [ScrapeController::class, 'results'])->name('scrapping.results');
Route::get('/scrapping/download', [ScrapeController::class, 'downloadCsv'])->name('scrapping.download');
Route::get('/scrapping/check-status', [ScrapeController::class, 'checkStatus'])->name('scrapping.checkStatus');

// -------------------
// Kliping Online & Offline
// -------------------
Route::get('/kliping', [KlipingController::class, 'form'])->name('kliping.form');
Route::post('/kliping/generate', [KlipingController::class, 'generate'])->name('kliping.generate');

// -------------------
// Klipings (multiple kliping management)
// -------------------
// Daftar kliping
// Form input kliping (langsung diarahkan dari menu "Klipings")//
Route::get('/klipings', [KlipingsController::class, 'create'])->name('klipings.form');

// Simpan kliping baru
Route::post('/klipings', [KlipingsController::class, 'store'])->name('klipings.store');

// Edit & Update
Route::get('/klipings/{id}/edit', [KlipingsController::class, 'edit'])->name('klipings.edit');
Route::put('/klipings/{id}', [KlipingsController::class, 'update'])->name('klipings.update');

// Detail kliping (opsional kalau masih dipakai)
Route::get('/klipings/{id}', [KlipingsController::class, 'show'])->name('klipings.show');

// Preview kliping dengan embed PDF viewer
Route::get('/klipings/{id}/preview', [KlipingsController::class, 'preview'])->name('klipings.preview');

// Streaming PDF langsung di browser
Route::get('/klipings/{id}/pdf', [KlipingsController::class, 'previewPdf'])->name('klipings.pdf');

// Download PDF
Route::get('/klipings/{id}/pdf/download', [KlipingsController::class, 'downloadPdf'])->name('klipings.download');