<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomepageController::class);
Route::get('/block/{hashOrHeight}', [BlockController::class, 'show'])->name('block.show');
Route::get('/tx/{txid}', [TransactionController::class, 'show'])->name('transaction.show');
Route::get('/address/{address}', [AddressController::class, 'show'])->name('address.show');


