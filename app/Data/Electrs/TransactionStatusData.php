<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class TransactionStatusData extends Data
{
    public function __construct(
        public bool $confirmed = false,
        #[MapInputName('block_height')]
        public ?int $blockHeight = null,
        #[MapInputName('block_hash')]
        public ?string $blockHash = null,
        #[MapInputName('block_time')]
        public ?int $blockTime = null,
    ) {}
}
