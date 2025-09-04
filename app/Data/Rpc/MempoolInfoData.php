<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class MempoolInfoData extends Data
{
    public function __construct(
        public int $size = 0,
        public int $bytes = 0,
        public int $usage = 0,
        #[MapInputName('maxmempool')]
        public int $maxMempool = 0,
        #[MapInputName('mempoolminfee')]
        public float $mempoolMinFee = 0.0,
    ) {}
}
