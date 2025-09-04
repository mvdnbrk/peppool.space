<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

final class ValidateAddressData extends Data
{
    public function __construct(
        #[MapInputName('isvalid')]
        public bool $isValid,
        #[MapInputName('ismine')]
        public bool $isMine,
        public ?string $address,
        public ?string $scriptPubKey,
    ) {}

    public static function fromRpc(array $payload): self
    {
        return new self(
            isValid: (bool) ($payload['isvalid'] ?? false),
            isMine: (bool) ($payload['ismine'] ?? false),
            address: isset($payload['address']) && is_string($payload['address']) ? $payload['address'] : null,
            scriptPubKey: isset($payload['scriptPubKey']) && is_string($payload['scriptPubKey']) ? $payload['scriptPubKey'] : null,
        );
    }
}
