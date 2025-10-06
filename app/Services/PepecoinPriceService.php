<?php

namespace App\Services;

use App\Jobs\CalculateTotalSupply;
use App\Models\Price;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PepecoinPriceService
{
    public function __construct(
        private int $priceCacheTtl = 300,
    ) {}

    public function getPrices(): Collection
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds($this->priceCacheTtl),
            function (): Collection {
                $prices = Price::whereIn('currency', ['EUR', 'USD'])
                    ->latest()
                    ->take(2)
                    ->get();

                $result = $prices->isNotEmpty()
                    ? $prices->pluck('price', 'currency')
                        ->merge(['timestamp' => $prices->first()->created_at->timestamp])
                        ->toArray()
                    : ['EUR' => 0, 'USD' => 0, 'timestamp' => time()];

                return new Collection($result);
            }
        );
    }

    public function getTotalSupply(bool $refresh = false): float
    {
        if ($refresh) {
            // Queue the job to update the cache in the background
            CalculateTotalSupply::dispatch();
        }

        return (float) Cache::get('pepe:total_supply', 0.0);
    }

    public function getMarketCap(string $currency = 'USD'): float
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__.'_'.$currency),
            Carbon::now()->addSeconds($this->priceCacheTtl),
            function () use ($currency): float {
                $prices = $this->getPrices();
                $price = $prices->get($currency);

                if (! $price || $price <= 0) {
                    return 0.0;
                }

                $supply = $this->getTotalSupply();
                if (! $supply || $supply === 0.0) {
                    return 0.0;
                }

                return (float) $supply * $price;
            }
        );
    }

    private function getCacheKey(string $key): string
    {
        return Str::of($key)
            ->replaceFirst('get', '')
            ->prepend('pep_price')
            ->snake()
            ->toString();
    }
}
