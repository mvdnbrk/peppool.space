<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\ScriptPubKeyData;
use PHPUnit\Framework\TestCase;

final class ScriptPubKeyDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = ScriptPubKeyData::fromRpc([
            'asm' => 'OP_DUP',
            'hex' => '76a9',
            'reqSigs' => 1,
            'type' => 'pubkeyhash',
            'addresses' => ['Pabc', 'Pdef'],
        ]);

        $this->assertSame('OP_DUP', $dto->asm);
        $this->assertSame('76a9', $dto->hex);
        $this->assertSame(1, $dto->reqSigs);
        $this->assertSame('pubkeyhash', $dto->type);
        $this->assertSame(['Pabc', 'Pdef'], $dto->addresses);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = ScriptPubKeyData::fromRpc([]);

        $this->assertSame('', $dto->asm);
        $this->assertSame('', $dto->hex);
        $this->assertNull($dto->reqSigs);
        $this->assertSame('', $dto->type);
        $this->assertSame([], $dto->addresses);
    }
}
