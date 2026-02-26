<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MiningPoolService
{
    /** @var Collection<int, Pool>|null */
    private ?Collection $pools = null;

    private function getPools(): Collection
    {
        if ($this->pools === null) {
            $this->pools = Cache::remember('mining_pools_list', 3600, function () {
                return Pool::all();
            });
        }

        return $this->pools;
    }

    public function identifyFromBlock(array $blockData): ?Pool
    {
        // 1. Try to identify from AuxPow (Parent block coinbase)
        if (isset($blockData['auxpow']['tx'])) {
            $parentCoinbase = $blockData['auxpow']['tx'];
            $scriptSig = $parentCoinbase['vin'][0]['coinbase'] ?? null;
            $payoutAddress = $parentCoinbase['vout'][0]['scriptPubKey']['addresses'][0] ?? null;

            $pool = $this->identifyPool($scriptSig, $payoutAddress);
            if ($pool) {
                return $pool;
            }
        }

        // 2. Try to identify from Pepecoin Coinbase (if tx data is available)
        if (isset($blockData['tx'][0]) && is_array($blockData['tx'][0])) {
            $coinbase = $blockData['tx'][0];
            $scriptSig = $coinbase['vin'][0]['coinbase'] ?? null;
            $payoutAddress = $coinbase['vout'][0]['scriptPubKey']['addresses'][0] ?? null;

            return $this->identifyPool($scriptSig, $payoutAddress);
        }

        return null;
    }

    public function identifyPool(?string $coinbaseScriptHex, ?string $payoutAddress): ?Pool
    {
        if (empty($coinbaseScriptHex) && empty($payoutAddress)) {
            return null;
        }

        $decodedScript = '';
        if (! empty($coinbaseScriptHex)) {
            $decodedScript = @hex2bin($coinbaseScriptHex) ?: '';
        }

        foreach ($this->getPools() as $pool) {
            // Check addresses
            if (! empty($payoutAddress) && in_array($payoutAddress, $pool->addresses, true)) {
                return $pool;
            }

            // Check regexes against decoded script
            if (! empty($decodedScript)) {
                foreach ($pool->regexes as $regex) {
                    if (@preg_match($regex, $decodedScript)) {
                        return $pool;
                    }
                }
            }
        }

        return null;
    }

    public function clearCache(): void
    {
        Cache::forget('mining_pools_list');
        $this->pools = null;
    }
}
