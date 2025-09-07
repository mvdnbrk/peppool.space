<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

final class ValidateAddressData extends Data
{
    public function __construct(
        #[MapInputName('isvalid')]
        #[MapName('isvalid')]
        public bool $isValid = false,
        #[MapInputName('ismine')]
        #[Hidden]
        public bool $isMine = false,
        #[MapInputName('isscript')]
        #[MapName('isscript')]
        public bool $isScript = false,
        #[MapInputName('iswatchonly')]
        #[MapName('iswatchonly')]
        public bool $isWatchOnly = false,
        public ?string $address = null,
        public ?string $scriptPubKey = null,
    ) {}
}
