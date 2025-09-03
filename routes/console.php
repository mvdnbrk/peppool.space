<?php

use App\Jobs\CalculateTotalSupply;
use App\Jobs\FetchPepePrice;
use App\Jobs\StorePepePrice;
use Illuminate\Support\Facades\Schedule;

Schedule::job(FetchPepePrice::class)->everyFifteenMinutes();
Schedule::job(StorePepePrice::class)->everyFifteenMinutes();

Schedule::command('pepe:sync:blocks')->everyMinute();
Schedule::command('pepe:sync:transactions')->hourly();
Schedule::job(CalculateTotalSupply::class)->hourly();
