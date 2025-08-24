<?php

namespace App\Services;

use App\Models\Price;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PepecoinExplorerService
{
    public function __construct(
        private readonly PepecoinRpcService $rpcService,
        private int $mempoolCacheTtl = 10,
    ) {}

    private function getCacheKey(string $key): string
    {
        return Str::of($key)
            ->replaceFirst('get', '')
            ->prepend('pep_explorer')
            ->snake()
            ->toString();
    }

    public function getBlockTipHeight(): int
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds(30),
            fn (): int => $this->rpcService->getBlockCount()
        );
    }

    public function getBlockTipHash(): string
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds(30),
            fn (): string => $this->rpcService->getBlockHash(
                $this->getBlockTipHeight()
            )
        );
    }

    public function getMempoolInfo(): Collection
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds($this->mempoolCacheTtl),
            fn (): Collection => new Collection($this->rpcService->getMempoolInfo())
        );
    }

    public function getMempoolTxIds(): Collection
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addseconds($this->mempoolCacheTtl),
            function (): Collection {
                return new Collection($this->rpcService->getRawMempool());
            }
        );
    }

    public function getPrices(): Collection
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addMinutes(5),
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
}
