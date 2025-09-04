<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\RawVoutData;
use App\Data\Rpc\ScriptPubKeyData;
use Tests\TestCase;

final class RawVoutDataTest extends TestCase
{
    public function test_from_rpc_maps_fields_with_script_pub_key(): void
    {
        $dto = RawVoutData::from([
            'value' => 12.5,
            'n' => 0,
            'scriptPubKey' => [
                'asm' => 'OP_DUP',
                'hex' => '76a9',
                'reqSigs' => 1,
                'type' => 'pubkeyhash',
                'addresses' => ['Pabc'],
            ],
        ]);

        $this->assertSame(12.5, $dto->value);
        $this->assertSame(0, $dto->n);
        $this->assertInstanceOf(ScriptPubKeyData::class, $dto->scriptPubKey);
        $this->assertSame('OP_DUP', $dto->scriptPubKey->asm);
        $this->assertSame('76a9', $dto->scriptPubKey->hex);
        $this->assertSame(1, $dto->scriptPubKey->reqSigs);
        $this->assertSame('pubkeyhash', $dto->scriptPubKey->type);
        $this->assertSame(['Pabc'], $dto->scriptPubKey->addresses);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = RawVoutData::from([]);

        $this->assertSame(0.0, $dto->value);
        $this->assertSame(0, $dto->n);
        $this->assertNull($dto->scriptPubKey);
    }
}
