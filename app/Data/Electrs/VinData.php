<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class VinData extends Data
{
    public function __construct(
        public ?string $txid = null,
        public ?int $vout = null,
        public ?VoutData $prevout = null,
        public ?string $scriptsig = null,
        #[MapInputName('scriptsig_asm')]
        public ?string $scriptsigAsm = null,
        #[MapInputName('is_coinbase')]
        public bool $isCoinbase = false,
        public int $sequence = 0,
    ) {}

    public function getValueInPep(): float
    {
        return ($this->prevout?->value ?? 0) / 100_000_000;
    }
}
