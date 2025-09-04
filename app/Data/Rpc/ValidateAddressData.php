<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class ValidateAddressData
{
    public function __construct(
        public bool $isValid,
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
