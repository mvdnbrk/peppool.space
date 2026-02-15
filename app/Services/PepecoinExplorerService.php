<?php

namespace App\Services;

use App\Data\Rpc\MempoolInfoData;
use App\Data\Rpc\TxOutSetInfoData;
use App\Data\Rpc\ValidateAddressData;
use App\Models\Block;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PepecoinExplorerService
{
    public function __construct(
        private readonly PepecoinRpcService $rpcService,
        private int $mempoolCacheTtl = 10,
        private int $difficultyCacheTtl = 180,
    ) {}

    private function getCacheKey(string $key): string
    {
        return Str::of($key)
            ->replaceFirst('get', '')
            ->prepend('pep_explorer')
            ->snake()
            ->value();
    }

    public function getAverageBlockTime(int $blockWindow = 50): float
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__.'_'.$blockWindow),
            Carbon::now()->addMinutes(10),
            function () use ($blockWindow): float {
                $blocks = Block::latest('created_at')
                    ->take($blockWindow)
                    ->get(['height', 'created_at']);

                if ($blocks->count() < 2) {
                    return 60.0; // PepeCoin target
                }

                $timeDifferences = $blocks->sliding(2)
                    ->map(function ($pair) {
                        [$current, $previous] = $pair->values();

                        return $previous->created_at->diffInSeconds($current->created_at);
                    });

                $avg = $timeDifferences->average();

                return $avg > 0 ? round($avg, 2) : 60.0;
            }
        );
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

    public function getDifficulty(): float
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds($this->difficultyCacheTtl),
            function (): float {
                return $this->rpcService->getBlockchainInfo()->difficulty;
            }
        );
    }

    public function getHashrate(): float
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds($this->difficultyCacheTtl),
            function (): float {
                $difficulty = $this->getDifficulty();

                // 2^32 ≈ 4294967296; target block time = 60 seconds for PepeCoin
                return $difficulty * 4294967296 / $this->getAverageBlockTime();
            }
        );
    }

    public function getMempoolInfo(): MempoolInfoData
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds($this->mempoolCacheTtl),
            fn (): MempoolInfoData => $this->rpcService->getMempoolInfo()
        );
    }

    public function getMempoolTxIds(): Collection
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds($this->mempoolCacheTtl),
            function (): Collection {
                return new Collection($this->rpcService->getRawMempool());
            }
        );
    }

    public function getChainSize(): int
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addHours(4),
            function (): int {
                return $this->rpcService->getBlockchainInfo()->sizeOnDisk;
            }
        );
    }

    public function getNetworkSubversion(): string
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addDay(),
            function (): string {
                return $this->rpcService->getNetworkInfo()->subversion;
            }
        );
    }

    public function getNetworkConnectionsCount(): int
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addMinutes(5),
            function (): int {
                return $this->rpcService->getNetworkInfo()->connections;
            }
        );
    }

    public function getTxOutSetInfoData(): TxOutSetInfoData
    {
        return TxOutSetInfoData::from(
            $this->rpcService->getTxOutSetInfo()
        );
    }

    public function validateAddress(string $address): ValidateAddressData
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__.'_'.hash('sha256', $address)),
            Carbon::now()->addHours(24), // Cache for 24 hours since address validity doesn't change
            function () use ($address): ValidateAddressData {
                return ValidateAddressData::from(
                    $this->rpcService->call('validateaddress', [$address])
                );
            }
        );
    }

    public function getMempoolEntryTime(string $txid): ?int
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__.'_'.$txid),
            Carbon::now()->addMinutes(10),
            function () use ($txid): ?int {
                try {
                    return $this->rpcService->getMempoolEntry($txid)['time'] ?? null;
                } catch (\Throwable) {
                    return null;
                }
            }
        );
    }

    public function getFeeEstimates(): array
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addMinutes(1),
            function (): array {
                $targets = [2, 3, 4, 5, 6, 10, 20, 144, 1008];
                $estimates = [];

                foreach ($targets as $target) {
                    try {
                        $result = $this->rpcService->call('estimatesmartfee', [$target]);
                        if (isset($result['feerate'])) {
                            // RPC returns BTC/kB (or PEPE/kB). Convert to sat/vB (RIBBITS/vB)
                            // 1 PEPE/kB = 100,000,000 RIBBITS / 1000 vB = 100,000 RIBBITS/vB
                            // Wait, Bitcoin core returns BTC/kvB.
                            // 0.01 PEPE/kB * 10^8 RIBBITS / 1000 bytes = 1000 RIBBITS/vB
                            $estimates[(string) $target] = round($result['feerate'] * 100_000, 2);
                        }
                    } catch (\Throwable) {
                        continue;
                    }
                }

                return $estimates;
            }
        );
    }
}
