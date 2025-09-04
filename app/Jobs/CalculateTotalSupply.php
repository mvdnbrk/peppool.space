<?php

namespace App\Jobs;

use App\Services\PepecoinExplorerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class CalculateTotalSupply implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(PepecoinExplorerService $explorer): void
    {
        Cache::put(
            'pepe:total_supply',
            $explorer->getTxOutSetInfoData()->totalAmount,
            Carbon::now()->addHour()
        );
    }
}
