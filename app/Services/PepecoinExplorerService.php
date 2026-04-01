<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\TxOutSetInfoData;
use App\Data\Blockchain\ValidateAddressData;
use App\Models\Block;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PepecoinExplorerService
{
    public function __construct(
        private readonly PepecoinRpcService $rpcService,
        private readonly BlockchainServiceInterface $blockchain,
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
        return (float) Cache::remember(
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

                return $avg > 0 ? round((float) $avg, 2) : 60.0;
            }
        );
    }

    public function getDifficulty(): float
    {
        return (float) Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds($this->difficultyCacheTtl),
            function (): float {
                return $this->rpcService->getBlockchainInfo()->difficulty;
            }
        );
    }

    public function getHashrate(): float
    {
        return (float) Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addSeconds($this->difficultyCacheTtl),
            function (): float {
                $difficulty = $this->getDifficulty();

                // 2^32 ≈ 4294967296; target block time = 60 seconds for PepeCoin
                return $difficulty * 4294967296 / $this->getAverageBlockTime();
            }
        );
    }

    public function getChainSize(): int
    {
        return (int) Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addHours(4),
            function (): int {
                return $this->rpcService->getBlockchainInfo()->sizeOnDisk;
            }
        );
    }

    public function getNetworkSubversion(): string
    {
        return (string) Cache::remember(
            $this->getCacheKey(__FUNCTION__),
            Carbon::now()->addDay(),
            function (): string {
                return $this->rpcService->getNetworkInfo()->subversion;
            }
        );
    }

    public function getNetworkConnectionsCount(): int
    {
        return (int) Cache::remember(
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
        $data = Cache::remember(
            $this->getCacheKey(__FUNCTION__.'_'.hash('sha256', $address)),
            Carbon::now()->addHours(24), // Cache for 24 hours since address validity doesn't change
            function () use ($address): array {
                return $this->rpcService->call('validateaddress', [$address]);
            }
        );

        return ValidateAddressData::from($data);
    }

    public function getMempoolEntryTime(string $txid): ?int
    {
        $value = Cache::remember(
            $this->getCacheKey(__FUNCTION__.'_'.$txid),
            Carbon::now()->addMinutes(10),
            function () use ($txid): ?int {
                try {
                    $entry = $this->rpcService->getMempoolEntry($txid);

                    return isset($entry['time']) ? (int) $entry['time'] : null;
                } catch (\Throwable) {
                    return null;
                }
            }
        );

        return $value === null ? null : (int) $value;
    }

    public function getRecommendedFees(): array
    {
        $estimates = $this->blockchain->getFeeEstimates();
        $network = $this->rpcService->getNetworkInfo();
        $minRelayFee = round($network->relayFee * 1000, 3);

        $fees = [
            'fastestFee' => $estimates['1'] ?? $estimates['2'] ?? $minRelayFee,
            'halfHourFee' => $estimates['6'] ?? $estimates['5'] ?? $minRelayFee,
            'hourFee' => $estimates['12'] ?? $estimates['10'] ?? $minRelayFee,
            'economyFee' => $estimates['144'] ?? $minRelayFee,
            'minimumFee' => $minRelayFee,
        ];

        // Ensure fees are never below the relay fee and are descending correctly
        $fees['fastestFee'] = max($fees['fastestFee'], $fees['halfHourFee']);
        $fees['halfHourFee'] = max($fees['halfHourFee'], $fees['hourFee']);
        $fees['hourFee'] = max($fees['hourFee'], $fees['economyFee']);
        $fees['economyFee'] = max($fees['economyFee'], $fees['minimumFee']);

        return $fees;
    }
}
