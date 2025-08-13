<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchPepePrice implements ShouldQueue
{
    use Queueable;

    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = Str::of(config('services.coingecko.base_url'))
            ->append('simple/price?ids=pepecoin-network&vs_currencies=usd')
            ->toString();
    }

    public function handle(): void
    {
        $response = Http::acceptJson()
            ->get($this->apiUrl);

        if ($response->successful()) {
            Cache::put('pepe_price', $response->json('pepecoin-network.usd'));
        }
    }
}
