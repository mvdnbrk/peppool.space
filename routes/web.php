<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomepageController::class);

Route::get('/block/{hashOrHeight}', [BlockController::class, 'show'])
    ->name('block.show')
    ->where('hashOrHeight', '[0-9]+|[0-9a-fA-F]{64}');

Route::get('/tx/{txid}', [TransactionController::class, 'show'])
    ->name('transaction.show')
    ->where('txid', '[0-9a-fA-F]{64}');

Route::get('/address/{address}', [AddressController::class, 'show'])
    ->name('address.show')
    ->where('address', 'P[1-9A-HJ-NP-Za-km-z]{25,33}');
