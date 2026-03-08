<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\AddressData;
use App\Data\Blockchain\BlockData;
use App\Data\Blockchain\MempoolData;
use App\Data\Blockchain\RecentMempoolTransactionData;
use App\Data\Blockchain\TransactionData;
use App\Data\Blockchain\TransactionStatusData;
use App\Exceptions\UnsupportedOperationException;
use Illuminate\Support\Collection;

class RpcBlockchainService implements BlockchainServiceInterface
{
    public function __construct(
        private readonly PepecoinRpcService $rpc,
    ) {}

    // -- Blocks --

    public function getBlockTipHeight(): int
    {
        return $this->rpc->getBlockCount();
    }

    public function getBlockTipHash(): string
    {
        return $this->rpc->getBlockHash($this->getBlockTipHeight());
    }

    public function getBlock(string $hash): BlockData
    {
        $block = $this->rpc->getBlock($hash, 1);

        return $this->transformBlock($block);
    }

    public function getBlockHash(int $height): string
    {
        return $this->rpc->getBlockHash($height);
    }

    public function getBlockTransactions(string $hash): Collection
    {
        $block = $this->rpc->getBlock($hash, 2); // verbosity 2 = full tx data

        return collect($block['tx'] ?? [])->map(fn (array $tx) => $this->transformTransaction($tx));
    }

    public function getBlockTxids(string $hash): array
    {
        $block = $this->rpc->getBlock($hash, 1);

        return $block['tx'] ?? [];
    }

    // -- Transactions --

    public function getTransaction(string $txid): TransactionData
    {
        $tx = $this->rpc->getRawTransaction($txid, true);

        return $this->transformTransaction($tx);
    }

    public function getTransactionStatus(string $txid): TransactionStatusData
    {
        $tx = $this->rpc->getRawTransaction($txid, true);
        $confirmed = isset($tx['blockhash']);

        return TransactionStatusData::from([
            'confirmed' => $confirmed,
            'block_height' => $confirmed ? ($tx['blockheight'] ?? null) : null,
            'block_hash' => $tx['blockhash'] ?? null,
            'block_time' => $tx['blocktime'] ?? null,
        ]);
    }

    public function getRawTransaction(string $txid): string
    {
        // verbose=false returns hex string
        return (string) $this->rpc->call('getrawtransaction', [$txid, false]);
    }

    public function broadcastTransaction(string $hex): string
    {
        return (string) $this->rpc->call('sendrawtransaction', [$hex]);
    }

    // -- Mempool --

    public function getMempool(): MempoolData
    {
        $info = $this->rpc->getMempoolInfo();

        return MempoolData::from([
            'count' => $info->size,
            'vsize' => $info->bytes,
            'total_fee' => 0, // RPC getmempoolinfo doesn't provide total fee
            'fee_histogram' => [],
        ]);
    }

    public function getMempoolTxIds(): Collection
    {
        return new Collection($this->rpc->getRawMempool());
    }

    public function getRecentMempoolTransactions(): Collection
    {
        $mempool = $this->rpc->getRawMempool(true);

        return collect($mempool)
            ->sortByDesc('time')
            ->take(10)
            ->map(fn (array $entry, string $txid) => RecentMempoolTransactionData::from([
                'txid' => $txid,
                'fee' => (int) round(($entry['fee'] ?? 0) * 100_000_000),
                'vsize' => $entry['vsize'] ?? $entry['size'] ?? 0,
                'value' => 0, // RPC mempool entries don't include output value
            ]))
            ->values();
    }

    public function getFeeEstimates(): array
    {
        // Moved from PepecoinExplorerService::getFeeEstimates()
        $targets = [1, 2, 3, 4, 5, 6, 10, 12, 20, 144, 1008];
        $estimates = [];

        foreach ($targets as $target) {
            try {
                /** @var array{feerate?: float} $result */
                $result = $this->rpc->call('estimatesmartfee', [$target]);
                if (isset($result['feerate'])) {
                    $estimates[(string) $target] = round($result['feerate'] * 1000, 3);
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return $estimates;
    }

    // -- Address (not supported via RPC) --

    public function getAddress(string $address): AddressData
    {
        throw UnsupportedOperationException::electrsRequired('getAddress');
    }

    public function getAddressTransactions(string $address, ?string $afterTxid = null): Collection
    {
        throw UnsupportedOperationException::electrsRequired('getAddressTransactions');
    }

    public function getAddressUtxos(string $address): Collection
    {
        throw UnsupportedOperationException::electrsRequired('getAddressUtxos');
    }

    // -- Private transform helpers --

    /**
     * @param  array<string, mixed>  $rpc
     */
    private function transformBlock(array $rpc): BlockData
    {
        return BlockData::from([
            'id' => $rpc['hash'],
            'height' => $rpc['height'],
            'version' => $rpc['version'],
            'timestamp' => $rpc['time'],
            'tx_count' => count($rpc['tx'] ?? []),
            'size' => $rpc['size'],
            'weight' => $rpc['weight'] ?? $rpc['size'] * 4,
            'merkle_root' => $rpc['merkleroot'],
            'previousblockhash' => $rpc['previousblockhash'] ?? null,
            'mediantime' => $rpc['mediantime'],
            'nonce' => $rpc['nonce'],
            'bits' => (string) hexdec((string) $rpc['bits']),
            'difficulty' => $rpc['difficulty'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $rpc
     */
    private function transformTransaction(array $rpc): TransactionData
    {
        $vin = collect($rpc['vin'] ?? [])->map(fn (array $in) => $this->transformVin($in))->toArray();
        $vout = collect($rpc['vout'] ?? [])->map(fn (array $out) => $this->transformVout($out))->toArray();

        $confirmed = isset($rpc['blockhash']);

        return TransactionData::from([
            'txid' => $rpc['txid'],
            'version' => $rpc['version'],
            'locktime' => $rpc['locktime'],
            'vin' => $vin,
            'vout' => $vout,
            'size' => $rpc['size'],
            'weight' => $rpc['weight'] ?? $rpc['size'] * 4,
            'fee' => 0, // RPC doesn't provide fee directly in getrawtransaction
            'status' => [
                'confirmed' => $confirmed,
                'block_height' => $confirmed ? ($rpc['blockheight'] ?? null) : null,
                'block_hash' => $rpc['blockhash'] ?? null,
                'block_time' => $rpc['blocktime'] ?? null,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $rpc
     * @return array<string, mixed>
     */
    private function transformVin(array $rpc): array
    {
        return [
            'txid' => $rpc['txid'] ?? null,
            'vout' => $rpc['vout'] ?? null,
            'prevout' => null, // RPC doesn't include prevout data
            'scriptsig' => $rpc['scriptSig']['hex'] ?? null,
            'scriptsig_asm' => $rpc['scriptSig']['asm'] ?? null,
            'is_coinbase' => isset($rpc['coinbase']),
            'sequence' => $rpc['sequence'] ?? 0,
        ];
    }

    /**
     * @param  array<string, mixed>  $rpc
     * @return array<string, mixed>
     */
    private function transformVout(array $rpc): array
    {
        return [
            'scriptpubkey' => $rpc['scriptPubKey']['hex'] ?? '',
            'scriptpubkey_asm' => $rpc['scriptPubKey']['asm'] ?? '',
            'scriptpubkey_type' => $rpc['scriptPubKey']['type'] ?? '',
            'scriptpubkey_address' => $rpc['scriptPubKey']['addresses'][0] ?? null,
            'value' => (int) round(($rpc['value'] ?? 0) * 100_000_000),
        ];
    }
}
