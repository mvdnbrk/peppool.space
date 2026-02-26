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
        $scriptSig = null;
        $payoutAddress = null;

        // 1. Try to identify from AuxPow (Parent block coinbase)
        if (isset($blockData['auxpow']['tx'])) {
            $parentCoinbase = $blockData['auxpow']['tx'];
            $scriptSig = $parentCoinbase['vin'][0]['coinbase'] ?? null;
            $payoutAddress = $parentCoinbase['vout'][0]['scriptPubKey']['addresses'][0] ?? null;

            $pool = $this->identifyPool($scriptSig, $payoutAddress);
            if ($pool) {
                // If identified by tag but address is different, we'll want to record the child address later
                return $pool;
            }
        }

        // 2. Try to identify from Pepecoin Coinbase
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

        $pools = Pool::all();

        foreach ($pools as $pool) {
            // Check addresses
            if (! empty($payoutAddress) && in_array($payoutAddress, $pool->addresses, true)) {
                return $pool;
            }

            // Check tags (case-insensitive substring match)
            if (! empty($decodedScript)) {
                foreach ($pool->regexes as $tag) {
                    if (str_contains(strtolower($decodedScript), strtolower((string) $tag))) {
                        return $pool;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Record a payout address for a pool if it's not already known.
     */
    public function recordPayoutAddress(Pool $pool, ?string $address): void
    {
        if (empty($address) || $pool->name === 'Unknown') {
            return;
        }

        if (! in_array($address, $pool->addresses, true)) {
            $addresses = $pool->addresses;
            $addresses[] = $address;

            // Keep a reasonable limit of addresses (e.g., last 5 used)
            if (count($addresses) > 5) {
                array_shift($addresses);
            }

            $pool->update(['addresses' => $addresses]);
            $this->clearCache();
        }
    }

    public function clearCache(): void
    {
        Cache::forget('mining_pools_list');
        $this->pools = null;
    }
}
