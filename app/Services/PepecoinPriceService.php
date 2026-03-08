<?php

namespace App\Services;

use App\Contracts\BlockchainServiceInterface;
use App\Models\Price;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PepecoinPriceService
{
    public function __construct(
        private readonly BlockchainServiceInterface $blockchain,
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

    public function getTotalSupply(): int
    {
        return Cache::remember('pepe:total_supply', 60, function (): int {
            $checkpoint = Cache::get('pepe:supply_checkpoint');

            if (! $checkpoint) {
                return 0;
            }

            $blockReward = config('pepecoin.chain.block_reward');

            try {
                $currentHeight = $this->blockchain->getBlockTipHeight();
                $blocksSince = max(0, $currentHeight - $checkpoint['height']);
                $supply = $checkpoint['supply'] + ($blocksSince * $blockReward);
            } catch (\Throwable) {
                $supply = $checkpoint['supply'];
            }

            return (int) (round($supply / $blockReward) * $blockReward);
        });
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
                if (! $supply || $supply === 0) {
                    return 0.0;
                }

                return $supply * $price;
            }
        );
    }

    private function getCacheKey(string $key): string
    {
        return Str::of($key)
            ->replaceFirst('get', '')
            ->prepend('pep_price')
            ->snake()
            ->value();
    }
}
