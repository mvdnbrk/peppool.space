<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class TransactionData
{
    public function __construct(
        public string $txid,
        public int $version,
        public int $size,
        public int $vsize,
        public int $locktime,
        public ?int $time,
        public ?int $blocktime,
        public ?string $blockhash,
    ) {}

    public static function fromRpc(array $payload): self
    {
        return new self(
            txid: (string) ($payload['txid'] ?? ''),
            version: (int) ($payload['version'] ?? 0),
            size: (int) ($payload['size'] ?? 0),
            vsize: (int) ($payload['vsize'] ?? ($payload['size'] ?? 0)),
            locktime: (int) ($payload['locktime'] ?? 0),
            time: isset($payload['time']) ? (int) $payload['time'] : null,
            blocktime: isset($payload['blocktime']) ? (int) $payload['blocktime'] : null,
            blockhash: isset($payload['blockhash']) && is_string($payload['blockhash']) ? $payload['blockhash'] : null,
        );
    }
}
