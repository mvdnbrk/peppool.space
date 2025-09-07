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
            'iswatchonly' => true,
            'address' => 'Pabc',
            'scriptPubKey' => '76a9',
            'isscript' => true,
        ]);

        $this->assertTrue($dto->isValid);
        $this->assertFalse($dto->isMine);
        $this->assertTrue($dto->isWatchOnly);
        $this->assertSame('Pabc', $dto->address);
        $this->assertSame('76a9', $dto->scriptPubKey);
        $this->assertTrue($dto->isScript);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = ValidateAddressData::from([]);

        $this->assertFalse($dto->isValid);
        $this->assertFalse($dto->isMine);
        $this->assertFalse($dto->isWatchOnly);
        $this->assertNull($dto->address);
        $this->assertNull($dto->scriptPubKey);
        $this->assertFalse($dto->isScript);
    }
}
