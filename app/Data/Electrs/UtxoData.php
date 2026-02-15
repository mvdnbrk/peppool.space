<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class UtxoData extends Data
{
    public function __construct(
        public string $txid,
        public int $vout,
        public TransactionStatusData $status,
        public int $value,
    ) {}

    public function getValueInPep(): float
    {
        return $this->value / 100_000_000;
    }
}
