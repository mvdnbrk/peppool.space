<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

final class MempoolInfoData extends Data
{
    public function __construct(
        public int $size,
        public int $bytes,
        public int $usage,
        #[MapInputName('maxmempool')]
        public int $maxMempool,
        #[MapInputName('mempoolminfee')]
        public float $mempoolMinFee,
    ) {}

    public static function fromRpc(array $payload): self
    {
        return new self(
            size: (int) ($payload['size'] ?? 0),
            bytes: (int) ($payload['bytes'] ?? 0),
            usage: (int) ($payload['usage'] ?? 0),
            maxMempool: (int) ($payload['maxmempool'] ?? 0),
            mempoolMinFee: (float) ($payload['mempoolminfee'] ?? 0.0),
        );
    }
}
