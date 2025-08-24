<?php

use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\MempoolController;
use App\Http\Controllers\Api\PricesController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->name('api.')->group(function () {
    Route::get('/blocks/tip/height', [BlockController::class, 'tipHeight'])->name('blocks.tip.height');
    Route::get('/blocks/tip/hash', [BlockController::class, 'tipHash'])->name('blocks.tip.hash');
    Route::get('/prices', PricesController::class)->name('prices');

    Route::prefix('mempool')->name('mempool.')->group(function () {
        Route::get('/', [MempoolController::class, 'index'])->name('index');
        Route::get('/txids', [MempoolController::class, 'txids'])->name('txids');
    });
});
