<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class StatsData extends Data
{
    public function __construct(
        #[MapInputName('funded_txo_count')]
        public int $fundedTxoCount = 0,
        #[MapInputName('funded_txo_sum')]
        public int $fundedTxoSum = 0,
        #[MapInputName('spent_txo_count')]
        public int $spentTxoCount = 0,
        #[MapInputName('spent_txo_sum')]
        public int $spentTxoSum = 0,
        #[MapInputName('tx_count')]
        public int $txCount = 0,
    ) {}

    public function getBalance(): float
    {
        return ($this->fundedTxoSum - $this->spentTxoSum) / 100_000_000;
    }

    public function getTotalReceived(): float
    {
        return $this->fundedTxoSum / 100_000_000;
    }

    public function getTotalSent(): float
    {
        return $this->spentTxoSum / 100_000_000;
    }
}
