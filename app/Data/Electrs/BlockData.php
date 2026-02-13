<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class BlockData extends Data
{
    public function __construct(
        public string $id,
        public int $height,
        public int $version,
        public int $timestamp,
        #[MapInputName('tx_count')]
        public int $txCount,
        public int $size,
        public int $weight,
        #[MapInputName('merkle_root')]
        public string $merkleRoot,
        public ?string $previousblockhash,
        #[MapInputName('mediantime')]
        public int $medianTime,
        public int $nonce,
        public int $bits,
        public float $difficulty,
    ) {}
}
