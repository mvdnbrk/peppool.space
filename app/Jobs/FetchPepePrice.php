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

    private array $currencies = [
        'usd',
        'eur',
    ];

    public function __construct()
    {
        $this->apiUrl = Str::of(config('services.coingecko.base_url'))
            ->append('simple/price?ids=pepecoin-network&vs_currencies=')
            ->append(implode(',', $this->currencies))
            ->value();
    }

    public function handle(): void
    {
        $response = Http::acceptJson()
            ->get($this->apiUrl);

        if ($response->successful()) {
            $data = $response->json('pepecoin-network');
            foreach ($this->currencies as $currency) {
                Cache::put("pepecoin_price_{$currency}", (float) $data[$currency], 3600);
            }
        }
    }
}
