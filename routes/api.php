<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\ChartPricesController;
use App\Http\Controllers\Api\MempoolController;
use App\Http\Controllers\Api\PricesController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->name('api.')->group(function () {
    Route::get('/blocks/tip/height', [BlockController::class, 'tipHeight'])->name('blocks.tip.height');
    Route::get('/blocks/tip/hash', [BlockController::class, 'tipHash'])->name('blocks.tip.hash');
    Route::get('/blocks/{startHeight?}', [BlockController::class, 'list'])->name('blocks.list');
    Route::get('/prices', PricesController::class)->name('prices');

    Route::get('/validate-address/{address}', [AddressController::class, 'validate'])
        ->name('validate.address');

    Route::get('/tx/{txid}', [TransactionController::class, 'show'])
        ->name('tx.show');

    Route::get('/tx/{txid}/status', [TransactionController::class, 'status'])
        ->name('tx.status');

    Route::get('/tx/{txid}/hex', [TransactionController::class, 'hex'])
        ->name('tx.hex');

    Route::get('/tx/{txid}/raw', [TransactionController::class, 'raw'])
        ->name('tx.raw');

    // Internal-only signed endpoint for chart time series
    Route::get('/chart', ChartPricesController::class)
        ->middleware('signed')
        ->name('chart.prices');

    Route::prefix('mempool')->name('mempool.')->group(function () {
        Route::get('/', [MempoolController::class, 'index'])->name('index');
        Route::get('/txids', [MempoolController::class, 'txids'])->name('txids');
        Route::get('/recent', [MempoolController::class, 'recent'])->name('recent');
    });
});
