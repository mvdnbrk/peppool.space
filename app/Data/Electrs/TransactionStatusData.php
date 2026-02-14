<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapOutputName(SnakeCaseMapper::class)]
final class TransactionStatusData extends Data
{
    public function __construct(
        public bool $confirmed = false,
        #[MapInputName('block_height')]
        public int|Optional $blockHeight = new Optional,
        #[MapInputName('block_hash')]
        public string|Optional $blockHash = new Optional,
        #[MapInputName('block_time')]
        public int|Optional $blockTime = new Optional,
    ) {}
}
