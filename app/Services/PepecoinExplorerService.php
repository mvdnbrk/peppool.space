<?php

namespace App\Services;

use App\Models\Block;
use App\Data\Rpc\TxOutSetInfoData;
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
            ->toString();
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

                $timeDifferences = Collection::make($blocks)
                    ->skip(1)
                    ->zip($blocks->take($blocks->count() - 1))
                    ->map(function ($pair) {
                        [$current, $previous] = $pair;

                        return $current->created_at->diffInSeconds($previous->created_at);
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
                return (new Collection($this->rpcService->getBlockchainInfo()))->get('difficulty', 0.0);
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
                return (int) (new Collection($this->rpcService->getBlockchainInfo()))
                    ->get('size_on_disk', 0);
            }
        );
    }

    public function getNetworkSubversion(): ?string
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addDay(),
            function (): ?string {
                return new Collection($this->rpcService->getNetworkInfo())
                    ->get('subversion');
            }
        );
    }

    public function getNetworkConnections(): int
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addMinutes(5),
            function (): int {
                return (int) (new Collection($this->rpcService->getNetworkInfo()))
                    ->get('connections', 0);
            }
        );
    }

    public function getTxOutSetInfoData(): TxOutSetInfoData
    {
        return TxOutSetInfoData::fromRpc(
            $this->rpcService->getTxOutSetInfo()
        );
    }

    public function validateAddress(string $address): Collection
    {
        return Cache::remember(
            $this->getCacheKey(__FUNCTION__.'_'.hash('sha256', $address)),
            Carbon::now()->addHours(24), // Cache for 24 hours since address validity doesn't change
            function () use ($address): Collection {
                try {
                    $result = $this->rpcService->call('validateaddress', [$address]);

                    return new Collection([
                        'isvalid' => $result['isvalid'] ?? false,
                        'ismine' => $result['ismine'] ?? false,
                        'iswatchonly' => $result['iswatchonly'] ?? false,
                        'isscript' => $result['isscript'] ?? false,
                        'pubkey' => $result['pubkey'] ?? null,
                        'scriptPubKey' => $result['scriptPubKey'] ?? null,
                        'address' => $result['address'] ?? $address,
                    ]);
                } catch (\Exception $e) {
                    return new Collection([
                        'isvalid' => false,
                        'ismine' => false,
                        'iswatchonly' => false,
                        'isscript' => false,
                        'pubkey' => null,
                        'address' => $address,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        );
    }
}
