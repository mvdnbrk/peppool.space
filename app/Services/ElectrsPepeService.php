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
use App\Data\Blockchain\UtxoData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ElectrsPepeService implements BlockchainServiceInterface
{
    private readonly string $url;

    public function __construct(?string $url = null)
    {
        $this->url = $url ?? config('pepecoin.electrs.url', 'http://127.0.0.1:3002');
    }

    public function getBlockTipHeight(): int
    {
        return (int) Http::get("{$this->url}/blocks/tip/height")
            ->throw()
            ->body();
    }

    public function getBlockTipHash(): string
    {
        return Http::get("{$this->url}/blocks/tip/hash")
            ->throw()
            ->body();
    }

    public function getMempool(): MempoolData
    {
        $response = Http::get("{$this->url}/mempool")
            ->throw()
            ->json();

        return MempoolData::from($response);
    }

    /** @return Collection<int, string> */
    public function getMempoolTxIds(): Collection
    {
        $response = Http::get("{$this->url}/mempool/txids")
            ->throw()
            ->json();

        return new Collection($response);
    }

    /** @return Collection<int, RecentMempoolTransactionData> */
    public function getRecentMempoolTransactions(): Collection
    {
        $response = Http::get("{$this->url}/mempool/recent")
            ->throw()
            ->json();

        return RecentMempoolTransactionData::collect($response, Collection::class);
    }

    public function getFeeEstimates(): array
    {
        return Http::get("{$this->url}/fee-estimates")
            ->throw()
            ->json();
    }

    public function getAddress(string $address): AddressData
    {
        $response = Http::get("{$this->url}/address/{$address}")
            ->throw()
            ->json();

        return AddressData::from($response);
    }

    /** @return Collection<int, TransactionData> */
    public function getAddressTransactions(string $address, ?string $afterTxid = null): Collection
    {
        $url = $afterTxid
            ? "{$this->url}/address/{$address}/txs/chain/{$afterTxid}"
            : "{$this->url}/address/{$address}/txs";

        $response = Http::get($url)
            ->throw()
            ->json();

        return TransactionData::collect($response, Collection::class);
    }

    /** @return Collection<int, UtxoData> */
    public function getAddressUtxos(string $address): Collection
    {
        $response = Http::get("{$this->url}/address/{$address}/utxo")
            ->throw()
            ->json();

        return UtxoData::collect($response, Collection::class);
    }

    public function getTransaction(string $txid): TransactionData
    {
        $response = Http::get("{$this->url}/tx/{$txid}")
            ->throw()
            ->json();

        return TransactionData::from($response);
    }

    public function getBlock(string $hash): BlockData
    {
        $response = Http::get("{$this->url}/block/{$hash}")
            ->throw()
            ->json();

        return BlockData::from($response);
    }

    public function getBlockHash(int $height): string
    {
        return Http::get("{$this->url}/block-height/{$height}")
            ->throw()
            ->body();
    }

    /** @return Collection<int, TransactionData> */
    public function getBlockTransactions(string $hash): Collection
    {
        $response = Http::get("{$this->url}/block/{$hash}/txs")
            ->throw()
            ->json();

        return TransactionData::collect($response, Collection::class);
    }

    public function getBlockTxids(string $hash): array
    {
        return Http::get("{$this->url}/block/{$hash}/txids")
            ->throw()
            ->json();
    }

    public function getTransactionStatus(string $txid): TransactionStatusData
    {
        $response = Http::get("{$this->url}/tx/{$txid}/status")
            ->throw()
            ->json();

        return TransactionStatusData::from($response);
    }

    public function getRawTransaction(string $txid): string
    {
        return Http::get("{$this->url}/tx/{$txid}/hex")
            ->throw()
            ->body();
    }

    public function broadcastTransaction(string $hex): string
    {
        return Http::withBody($hex, 'text/plain')
            ->post("{$this->url}/tx")
            ->throw()
            ->body();
    }
}
