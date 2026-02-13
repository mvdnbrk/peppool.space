<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

final class TransactionData extends Data
{
    public function __construct(
        public string $txid,
        public int $version,
        public int $locktime,
        #[DataCollectionOf(VinData::class)]
        public array $vin,
        #[DataCollectionOf(VoutData::class)]
        public array $vout,
        public int $size,
        public int $weight,
        public int $fee,
        public TransactionStatusData $status,
    ) {}

    public function getFeeInPep(): float
    {
        return $this->fee / 100_000_000;
    }

    public function getTotalInputValueInPep(): float
    {
        return collect($this->vin)->sum(fn (VinData $in) => $in->getValueInPep());
    }

    public function getTotalOutputValueInPep(): float
    {
        return collect($this->vout)->sum(fn (VoutData $out) => $out->getValueInPep());
    }
}
