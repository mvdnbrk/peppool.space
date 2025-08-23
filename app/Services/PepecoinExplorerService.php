<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PepecoinExplorerService
{
    private readonly PepecoinRpcService $rpcService;

    public function __construct(PepecoinRpcService $rpcService)
    {
        $this->rpcService = $rpcService;
    }

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
            Carbon::now()->addSeconds(10),
            fn (): Collection => new Collection($this->rpcService->getMempoolInfo())
        );
    }
}
