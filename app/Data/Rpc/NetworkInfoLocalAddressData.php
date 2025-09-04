<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class NetworkInfoLocalAddressData
{
    public function __construct(
        public string $address,
        public int $port,
        public int $score,
    ) {}

    public static function fromRpc(array $payload): self
    {
        return new self(
            address: (string) ($payload['address'] ?? ''),
            port: (int) ($payload['port'] ?? 0),
            score: (int) ($payload['score'] ?? 0),
        );
    }
}
