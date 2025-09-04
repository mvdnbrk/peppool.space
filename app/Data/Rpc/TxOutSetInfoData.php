<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class TxOutSetInfoData
{
    public function __construct(
        public int $height,
        public string $bestblock,
        public int $transactions,
        public int $txouts,
        public int $bytesSerialized,
        public string $hashSerialized,
        public float $totalAmount,
    ) {}

    public static function fromRpc(array $payload): self
    {
        return new self(
            height: (int) ($payload['height'] ?? 0),
            bestblock: (string) ($payload['bestblock'] ?? ''),
            transactions: (int) ($payload['transactions'] ?? 0),
            txouts: (int) ($payload['txouts'] ?? 0),
            bytesSerialized: (int) ($payload['bytes_serialized'] ?? 0),
            hashSerialized: (string) ($payload['hash_serialized'] ?? ''),
            totalAmount: (float) ($payload['total_amount'] ?? 0.0),
        );
    }
}
