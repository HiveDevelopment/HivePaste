<?php

use App\Http\Controllers\Api\PasteApiController;
use App\Http\Controllers\Api\HivePanelPasteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/integrations/hivepanel/pastes', [HivePanelPasteController::class, 'store'])
        ->middleware('throttle:paste-hivepanel-public');
    Route::get('/pastes/{paste:slug}', [PasteApiController::class, 'show'])->middleware('throttle:60,1');
    Route::middleware(['paste.api', 'throttle:paste-api'])->group(function (): void {
        Route::post('/pastes', [PasteApiController::class, 'store']);
        Route::delete('/pastes/{paste:slug}', [PasteApiController::class, 'destroy']);
    });
});
