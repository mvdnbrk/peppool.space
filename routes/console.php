<?php

use App\Jobs\StorePepePrice;
use Illuminate\Support\Facades\Schedule;

// Fetch fresh prices from CoinGecko
Schedule::command('pepe:price')->everyFifteenMinutes();

// Store prices at quarter-hour marks
Schedule::job(StorePepePrice::class)->everyFifteenMinutes();
