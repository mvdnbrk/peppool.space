<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
final class VoutData extends Data
{
    public function __construct(
        public string $scriptpubkey = '',
        #[MapInputName('scriptpubkey_asm')]
        public string $scriptpubkeyAsm = '',
        #[MapInputName('scriptpubkey_type')]
        public string $scriptpubkeyType = '',
        #[MapInputName('scriptpubkey_address')]
        public ?string $scriptpubkeyAddress = null,
        public int $value = 0,
    ) {}

    public function getValueInPep(): float
    {
        return $this->value / 100_000_000;
    }
}
