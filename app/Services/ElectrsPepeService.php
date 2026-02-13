<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Electrs\AddressData;
use Illuminate\Support\Facades\Http;

class ElectrsPepeService
{
    private readonly string $url;

    public function __construct(?string $url = null)
    {
        $this->url = $url ?? config('pepecoin.electrs.url', 'http://127.0.0.1:3002');
    }

    public function getAddress(string $address): AddressData
    {
        $response = Http::get("{$this->url}/address/{$address}")
            ->throw()
            ->json();

        return AddressData::from($response);
    }

    public function getAddressTransactions(string $address): array
    {
        return Http::get("{$this->url}/address/{$address}/txs")
            ->throw()
            ->json();
    }

    public function getAddressUtxos(string $address): array
    {
        return Http::get("{$this->url}/address/{$address}/utxo")
            ->throw()
            ->json();
    }

    public function getTransaction(string $txid): array
    {
        return Http::get("{$this->url}/tx/{$txid}")
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
}
