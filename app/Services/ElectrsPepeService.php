<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Electrs\AddressData;
use App\Data\Electrs\BlockData;
use App\Data\Electrs\MempoolData;
use App\Data\Electrs\RecentMempoolTransactionData;
use App\Data\Electrs\TransactionData;
use App\Data\Electrs\UtxoData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ElectrsPepeService
{
    private readonly string $url;

    public function __construct(?string $url = null)
    {
        $this->url = $url ?? config('pepecoin.electrs.url', 'http://127.0.0.1:3002');
    }

    public function getMempool(): MempoolData
    {
        $response = Http::get("{$this->url}/mempool")
            ->throw()
            ->json();

        return MempoolData::from($response);
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
    public function getAddressTransactions(string $address): Collection
    {
        $response = Http::get("{$this->url}/address/{$address}/txs")
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

    public function getTransactionStatus(string $txid): array
    {
        return Http::get("{$this->url}/tx/{$txid}/status")
            ->throw()
            ->json();
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
