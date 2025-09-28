<?php

use App\Http\Controllers\Api\KlipingsApiController;

Route::prefix('klipings')->group(function () {
    Route::post('generate-download', [KlipingsApiController::class, 'storeAndDownload']);
    Route::get('/', [KlipingsApiController::class, 'index']);
    Route::post('/', [KlipingsApiController::class, 'store']);
    Route::get('{id}', [KlipingsApiController::class, 'show']);
    Route::post('{id}', [KlipingsApiController::class, 'update']);
    Route::delete('{id}', [KlipingsApiController::class, 'destroy']);
});



