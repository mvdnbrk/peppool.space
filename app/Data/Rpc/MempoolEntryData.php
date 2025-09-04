<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class MempoolEntryData extends Data
{
    /** @param string[] $depends */
    public function __construct(
        public int $size = 0,
        public float $fee = 0.0,
        #[MapInputName('modifiedfee')]
        public float $modifiedFee = 0.0,
        public int $time = 0,
        public int $height = 0,
        #[MapInputName('startingpriority')]
        public float $startingPriority = 0.0,
        #[MapInputName('currentpriority')]
        public float $currentPriority = 0.0,
        #[MapInputName('descendantcount')]
        public int $descendantCount = 0,
        #[MapInputName('descendantsize')]
        public int $descendantSize = 0,
        #[MapInputName('descendantfees')]
        public int $descendantFees = 0,
        #[MapInputName('ancestorcount')]
        public int $ancestorCount = 0,
        #[MapInputName('ancestorsize')]
        public int $ancestorSize = 0,
        #[MapInputName('ancestorfees')]
        public int $ancestorFees = 0,
        public array $depends = [],
    ) {}
}
