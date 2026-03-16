<?php

namespace App\Jobs;

use App\Services\PepecoinExplorerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CalculateTotalSupply implements ShouldQueue
{
    use Dispatchable, Queueable;

    public int $timeout = 600;

    public function handle(PepecoinExplorerService $explorer): void
    {
        try {
            $data = $explorer->getTxOutSetInfoData();

            Cache::forever('pepe:supply_checkpoint', [
                'supply' => (int) $data->totalAmount,
                'height' => $data->height,
            ]);
        } catch (\Throwable $e) {
            Log::warning('CalculateTotalSupply: gettxoutsetinfo failed, keeping previous checkpoint.', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
