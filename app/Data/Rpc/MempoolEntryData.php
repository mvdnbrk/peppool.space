<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class MempoolEntryData
{
    /** @param string[] $depends */
    public function __construct(
        public int $size,
        public float $fee,
        public float $modifiedFee,
        public int $time,
        public int $height,
        public float $startingPriority,
        public float $currentPriority,
        public int $descendantCount,
        public int $descendantSize,
        public int $descendantFees,
        public int $ancestorCount,
        public int $ancestorSize,
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
