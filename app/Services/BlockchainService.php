<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\AddressData;
use App\Data\Blockchain\BlockData;
use App\Data\Blockchain\MempoolData;
use App\Data\Blockchain\TransactionData;
use App\Data\Blockchain\TransactionStatusData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BlockchainService implements BlockchainServiceInterface
{
    public function __construct(
        private readonly ElectrsPepeService $electrs,
        private readonly RpcBlockchainService $rpc,
    ) {}

    public function isElectrsAvailable(): bool
    {
        if (! config('pepecoin.electrs.enabled', true)) {
            return false;
        }

        return (bool) Cache::remember('blockchain_electrs_available', 30, function (): bool {
            try {
                $this->electrs->getBlockTipHeight();

                return true;
            } catch (\Throwable) {
                return false;
            }
        });
    }

    private function resolve(callable $electrs, callable $rpc): mixed
    {
        if (! $this->isElectrsAvailable()) {
            return $rpc();
        }

        try {
            return $electrs();
        } catch (\Throwable) {
            return $rpc();
        }
    }

    private function resolveElectrsOnly(callable $electrs): mixed
    {
        // Still try even if health check failed — let the exception propagate
        return $electrs();
    }

    public function getBlockTipHeight(): int
    {
        return $this->resolve(fn () => $this->electrs->getBlockTipHeight(), fn () => $this->rpc->getBlockTipHeight());
    }

    public function getBlockTipHash(): string
    {
        return $this->resolve(fn () => $this->electrs->getBlockTipHash(), fn () => $this->rpc->getBlockTipHash());
    }

    public function getBlock(string $hash): BlockData
    {
        return $this->resolve(fn () => $this->electrs->getBlock($hash), fn () => $this->rpc->getBlock($hash));
    }

    public function getBlockHash(int $height): string
    {
        return $this->resolve(fn () => $this->electrs->getBlockHash($height), fn () => $this->rpc->getBlockHash($height));
    }

    public function getBlockTransactions(string $hash): Collection
    {
        return $this->resolve(fn () => $this->electrs->getBlockTransactions($hash), fn () => $this->rpc->getBlockTransactions($hash));
    }

    public function getBlockTxids(string $hash): array
    {
        return $this->resolve(fn () => $this->electrs->getBlockTxids($hash), fn () => $this->rpc->getBlockTxids($hash));
    }

    public function getTransaction(string $txid): TransactionData
    {
        return $this->resolve(fn () => $this->electrs->getTransaction($txid), fn () => $this->rpc->getTransaction($txid));
    }

    public function getTransactionStatus(string $txid): TransactionStatusData
    {
        return $this->resolve(fn () => $this->electrs->getTransactionStatus($txid), fn () => $this->rpc->getTransactionStatus($txid));
    }

    public function getRawTransaction(string $txid): string
    {
        return $this->resolve(fn () => $this->electrs->getRawTransaction($txid), fn () => $this->rpc->getRawTransaction($txid));
    }

    public function broadcastTransaction(string $hex): string
    {
        return $this->resolve(fn () => $this->electrs->broadcastTransaction($hex), fn () => $this->rpc->broadcastTransaction($hex));
    }

    public function getMempool(): MempoolData
    {
        return $this->resolve(fn () => $this->electrs->getMempool(), fn () => $this->rpc->getMempool());
    }

    public function getMempoolTxIds(): Collection
    {
        return $this->resolve(fn () => $this->electrs->getMempoolTxIds(), fn () => $this->rpc->getMempoolTxIds());
    }

    public function getRecentMempoolTransactions(): Collection
    {
        return $this->resolve(fn () => $this->electrs->getRecentMempoolTransactions(), fn () => $this->rpc->getRecentMempoolTransactions());
    }

    public function getFeeEstimates(): array
    {
        return $this->resolve(fn () => $this->electrs->getFeeEstimates(), fn () => $this->rpc->getFeeEstimates());
    }

    public function getAddress(string $address): AddressData
    {
        return $this->resolveElectrsOnly(fn () => $this->electrs->getAddress($address));
    }

    public function getAddressTransactions(string $address, ?string $afterTxid = null): Collection
    {
        return $this->resolveElectrsOnly(fn () => $this->electrs->getAddressTransactions($address, $afterTxid));
    }

    public function getAddressUtxos(string $address): Collection
    {
        return $this->resolveElectrsOnly(fn () => $this->electrs->getAddressUtxos($address));
    }
}
