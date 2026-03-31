<?php

namespace App\Jobs;

use App\Models\Price;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
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

        if (! $response->successful()) {
            return;
        }

        $data = $response->json('pepecoin-network');
        $timestamp = $this->getRoundedTimestamp();

        foreach ($this->currencies as $currency) {
            $price = (float) $data[$currency];

            Cache::put("pepecoin_price_{$currency}", $price, 3600);

            Price::updateOrCreate([
                'currency' => Str::upper($currency),
                'created_at' => $timestamp,
            ], [
                'price' => $price,
            ]);
        }
    }

    private function getRoundedTimestamp(): Carbon
    {
        return Carbon::now()
            ->startOfHour()
            ->addMinutes(
                15 * floor(Carbon::now()->minute / 15)
            );
    }
}
