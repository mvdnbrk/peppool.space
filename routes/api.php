<?php

use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\MempoolController;
use App\Http\Controllers\Api\PriceController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/blocks/tip/height', [BlockController::class, 'tipHeight']);
    Route::get('/blocks/tip/hash', [BlockController::class, 'tipHash']);
    Route::get('/mempool', [MempoolController::class, 'index']);
    Route::get('/prices', PriceController::class);
});
