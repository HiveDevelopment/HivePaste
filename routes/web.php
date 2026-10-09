<?php

use App\Http\Controllers\WebPasteController;
use App\Http\Controllers\PasteReportController;
use Illuminate\Support\Facades\Route;

Route::get('/privacy', fn () => view('legal', ['type' => 'privacy', 'heading' => 'Privacy']))->name('privacy');
Route::get('/terms', fn () => view('legal', ['type' => 'terms', 'heading' => 'Terms of use']))->name('terms');
Route::get('/abuse', fn () => view('legal', ['type' => 'abuse', 'heading' => 'Report abuse']))->name('abuse');
Route::get('/', [WebPasteController::class, 'index'])->name('home');
Route::post('/redaction-preview', [WebPasteController::class, 'redactionPreview'])->middleware('throttle:paste-web')->name('pastes.redaction-preview');
Route::post('/pastes', [WebPasteController::class, 'store'])->middleware('throttle:paste-web')->name('pastes.store');
Route::get('/download/{paste:slug}', [WebPasteController::class, 'download'])->name('pastes.download');
Route::get('/raw/{paste:slug}', [WebPasteController::class, 'raw'])->name('pastes.raw');
Route::post('/{paste:slug}/report', [PasteReportController::class, 'store'])->middleware('throttle:paste-report')->name('pastes.report');
Route::get('/{paste:slug}/manage', [WebPasteController::class, 'manage'])->name('pastes.manage');
Route::get('/{paste:slug}/edit', [WebPasteController::class, 'edit'])->name('pastes.edit');
Route::put('/{paste:slug}', [WebPasteController::class, 'update'])->middleware('throttle:paste-web')->name('pastes.update');
Route::delete('/{paste:slug}', [WebPasteController::class, 'destroy'])->middleware('throttle:paste-web')->name('pastes.destroy');
Route::get('/{paste:slug}', [WebPasteController::class, 'show'])->name('pastes.show');
