<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class TxOutSetInfoData extends Data
{
    public function __construct(
        public int $height = 0,
        public string $bestblock = '',
        public int $transactions = 0,
        public int $txouts = 0,
        #[MapInputName('bytes_serialized')]
        public int $bytesSerialized = 0,
        #[MapInputName('hash_serialized')]
        public string $hashSerialized = '',
        #[MapInputName('total_amount')]
        public float $totalAmount = 0.0,
    ) {}
}
