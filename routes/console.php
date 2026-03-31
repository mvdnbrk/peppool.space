<?php

use App\Jobs\CalculateMiningStats;
use App\Jobs\CalculateTotalSupply;
use App\Jobs\FetchPepePrice;
use Illuminate\Support\Facades\Schedule;

Schedule::job(FetchPepePrice::class)->everyFifteenMinutes();

Schedule::command('pepe:sync:blocks')->everyMinute();
Schedule::command('pepe:sync:nodes')->everyFifteenMinutes();
Schedule::job(CalculateTotalSupply::class)->everySixHours();
Schedule::job(CalculateMiningStats::class)->hourly();
Schedule::command('pepe:backfill:inscription-counts')->everyFiveMinutes();
