<?php

use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\MempoolController;
use App\Http\Controllers\Api\PricesController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/blocks/tip/height', [BlockController::class, 'tipHeight']);
    Route::get('/blocks/tip/hash', [BlockController::class, 'tipHash']);
    Route::prefix('mempool')->group(function () {
        Route::get('/', [MempoolController::class, 'index']);
        Route::get('/txids', [MempoolController::class, 'txids']);
    });
    Route::get('/prices', PricesController::class);
});
