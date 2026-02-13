<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class MempoolData extends Data
{
    public function __construct(
        public int $count = 0,
        public int $vsize = 0,
        #[MapInputName('total_fee')]
        public int $totalFee = 0,
        #[MapInputName('fee_histogram')]
        public array $feeHistogram = [],
    ) {}

    public function getTotalFeeInPep(): float
    {
        return $this->totalFee / 100_000_000;
    }
}
