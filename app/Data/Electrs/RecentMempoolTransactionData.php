<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Data;

final class RecentMempoolTransactionData extends Data
{
    public function __construct(
        public string $txid,
        public int $fee,
        public int $vsize,
        public int $value,
    ) {}

    public function getFeeInPep(): float
    {
        return $this->fee / 100_000_000;
    }

    public function getValueInPep(): float
    {
        return $this->value / 100_000_000;
    }
}
