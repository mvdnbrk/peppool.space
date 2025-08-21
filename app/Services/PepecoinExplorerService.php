<?php

namespace App\Services;

use Illuminate\Support\Carbon;
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
}
