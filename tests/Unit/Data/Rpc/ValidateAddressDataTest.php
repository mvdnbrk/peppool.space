<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\ValidateAddressData;
use Tests\TestCase;

final class ValidateAddressDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = ValidateAddressData::from([
            'isvalid' => true,
            'ismine' => false,
            'address' => 'Pabc',
            'scriptPubKey' => '76a9',
        ]);

        $this->assertTrue($dto->isValid);
        $this->assertFalse($dto->isMine);
        $this->assertSame('Pabc', $dto->address);
        $this->assertSame('76a9', $dto->scriptPubKey);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = ValidateAddressData::from([]);

        $this->assertFalse($dto->isValid);
        $this->assertFalse($dto->isMine);
        $this->assertNull($dto->address);
        $this->assertNull($dto->scriptPubKey);
    }
}
