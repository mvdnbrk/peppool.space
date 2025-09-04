<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

final class TxOutSetInfoData extends Data
{
    public function __construct(
        public int $height,
        public string $bestblock,
        public int $transactions,
        public int $txouts,
        #[MapInputName('bytes_serialized')]
        public int $bytesSerialized,
        #[MapInputName('hash_serialized')]
        public string $hashSerialized,
        #[MapInputName('total_amount')]
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
