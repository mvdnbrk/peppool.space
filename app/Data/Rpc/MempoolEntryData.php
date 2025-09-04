<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

final class MempoolEntryData extends Data
{
    /** @param string[] $depends */
    public function __construct(
        public int $size,
        public float $fee,
        #[MapInputName('modifiedfee')]
        public float $modifiedFee,
        public int $time,
        public int $height,
        #[MapInputName('startingpriority')]
        public float $startingPriority,
        #[MapInputName('currentpriority')]
        public float $currentPriority,
        #[MapInputName('descendantcount')]
        public int $descendantCount,
        #[MapInputName('descendantsize')]
        public int $descendantSize,
        #[MapInputName('descendantfees')]
        public int $descendantFees,
        #[MapInputName('ancestorcount')]
        public int $ancestorCount,
        #[MapInputName('ancestorsize')]
        public int $ancestorSize,
        #[MapInputName('ancestorfees')]
        public int $ancestorFees,
        public array $depends,
    ) {}

    public static function fromRpc(array $payload): self
    {
        $depends = [];
        if (isset($payload['depends']) && is_array($payload['depends'])) {
            foreach ($payload['depends'] as $dep) {
                if (is_string($dep)) {
                    $depends[] = $dep;
                }
            }
        }

        return new self(
            size: (int) ($payload['size'] ?? 0),
            fee: (float) ($payload['fee'] ?? 0.0),
            modifiedFee: (float) ($payload['modifiedfee'] ?? 0.0),
            time: (int) ($payload['time'] ?? 0),
            height: (int) ($payload['height'] ?? 0),
            startingPriority: (float) ($payload['startingpriority'] ?? 0.0),
            currentPriority: (float) ($payload['currentpriority'] ?? 0.0),
            descendantCount: (int) ($payload['descendantcount'] ?? 0),
            descendantSize: (int) ($payload['descendantsize'] ?? 0),
            descendantFees: (int) ($payload['descendantfees'] ?? 0),
            ancestorCount: (int) ($payload['ancestorcount'] ?? 0),
            ancestorSize: (int) ($payload['ancestorsize'] ?? 0),
            ancestorFees: (int) ($payload['ancestorfees'] ?? 0),
            depends: $depends,
        );
    }
}
