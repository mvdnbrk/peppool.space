<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\Blockchain\AddressData;
use App\Data\Blockchain\BlockData;
use App\Data\Blockchain\MempoolData;
use App\Data\Blockchain\RecentMempoolTransactionData;
use App\Data\Blockchain\TransactionData;
use App\Data\Blockchain\TransactionStatusData;
use App\Data\Blockchain\UtxoData;
use Illuminate\Support\Collection;

interface BlockchainServiceInterface
{
    // Blocks
    public function getBlockTipHeight(): int;
    public function getBlockTipHash(): string;
    public function getBlock(string $hash): BlockData;
    public function getBlockHash(int $height): string;
    /** @return Collection<int, TransactionData> */
    public function getBlockTransactions(string $hash): Collection;
    /** @return array<int, string> */
    public function getBlockTxids(string $hash): array;

    // Transactions
    public function getTransaction(string $txid): TransactionData;
    public function getTransactionStatus(string $txid): TransactionStatusData;
    public function getRawTransaction(string $txid): string;
    public function broadcastTransaction(string $hex): string;

    // Mempool
    public function getMempool(): MempoolData;
    /** @return Collection<int, string> */
    public function getMempoolTxIds(): Collection;
    /** @return Collection<int, RecentMempoolTransactionData> */
    public function getRecentMempoolTransactions(): Collection;
    /** @return array<string, float> */
    public function getFeeEstimates(): array;

    // Address (throws UnsupportedOperationException when electrs unavailable)
    public function getAddress(string $address): AddressData;
    /** @return Collection<int, TransactionData> */
    public function getAddressTransactions(string $address): Collection;
    /** @return Collection<int, UtxoData> */
    public function getAddressUtxos(string $address): Collection;
}
