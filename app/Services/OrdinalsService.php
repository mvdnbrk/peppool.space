<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Ordinals\InscriptionData;
use Illuminate\Support\Facades\Http;

class OrdinalsService
{
    private readonly string $url;

    public function __construct()
    {
        $this->url = rtrim(config('pepecoin.ordinals.url'), '/');
    }

    public function getInscription(string $inscriptionId): InscriptionData
    {
        $response = Http::acceptJson()
            ->timeout(config('pepecoin.ordinals.timeout', 10))
            ->get("{$this->url}/inscription/{$inscriptionId}")
            ->throw()
            ->json();

        return InscriptionData::from($response);
    }

    public function getInscriptions(?int $page = null): array
    {
        $query = $page ? ['page' => $page] : [];

        return Http::acceptJson()
            ->timeout(config('pepecoin.ordinals.timeout', 10))
            ->get("{$this->url}/inscriptions", $query)
            ->throw()
            ->json();
    }

    public function getStatus(): array
    {
        return Http::acceptJson()
            ->timeout(config('pepecoin.ordinals.timeout', 10))
            ->get("{$this->url}/status")
            ->throw()
            ->json();
    }

    public function getOutput(string $outpoint): array
    {
        return Http::acceptJson()
            ->timeout(config('pepecoin.ordinals.timeout', 10))
            ->get("{$this->url}/output/{$outpoint}")
            ->throw()
            ->json();
    }

    public function getAddressInscriptions(string $address): array
    {
        return Http::acceptJson()
            ->timeout(config('pepecoin.ordinals.timeout', 10))
            ->get("{$this->url}/address/{$address}")
            ->throw()
            ->json();
    }

    public function getBlockInscriptionCount(int $height): int
    {
        $response = Http::acceptJson()
            ->timeout(config('pepecoin.ordinals.timeout', 10))
            ->get("{$this->url}/block/{$height}")
            ->throw()
            ->json();

        return count($response['inscriptions'] ?? []);
    }

    public function getContent(string $inscriptionId): \Illuminate\Http\Client\Response
    {
        return Http::timeout(config('pepecoin.ordinals.timeout', 10))
            ->get("{$this->url}/content/{$inscriptionId}")
            ->throw();
    }
}
