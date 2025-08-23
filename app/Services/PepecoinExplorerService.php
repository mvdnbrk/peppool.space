<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PepecoinExplorerService
{
    private readonly PepecoinRpcService $rpcService;

    public function __construct(PepecoinRpcService $rpcService)
    {
        $this->rpcService = $rpcService;
    }

    public function getBlockTipHeight(): int
    {
        return Cache::remember(
            'explorer_block_tip_height',
            Carbon::now()->addSeconds(30),
            fn (): int => $this->rpcService->getBlockCount()
        );
    }

    public function getBlockTipHash(): string
    {
        return Cache::remember(
            'explorer_block_tip_hash',
            Carbon::now()->addSeconds(30),
            fn (): string => $this->rpcService->getBlockHash(
                $this->getBlockTipHeight()
            )
        );
    }

    public function getMempoolInfo(): Collection
    {
        return Cache::remember(
            'explorer_mempool_info',
            Carbon::now()->addSeconds(10),
            fn (): Collection => new Collection($this->rpcService->getMempoolInfo())
        );
    }
}
