<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class NetworkInfoNetworkData
{
    public function __construct(
        public string $name,
        public bool $limited,
        public bool $reachable,
        public string $proxy,
        public bool $proxyRandomizeCredentials,
    ) {}

    public static function fromRpc(array $payload): self
    {
        return new self(
            name: (string) ($payload['name'] ?? ''),
            limited: (bool) ($payload['limited'] ?? false),
            reachable: (bool) ($payload['reachable'] ?? false),
            proxy: (string) ($payload['proxy'] ?? ''),
            proxyRandomizeCredentials: (bool) ($payload['proxy_randomize_credentials'] ?? false),
        );
    }
}
