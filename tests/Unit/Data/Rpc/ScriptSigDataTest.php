<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\ScriptSigData;
use PHPUnit\Framework\TestCase;

final class ScriptSigDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = ScriptSigData::fromRpc([
            'asm' => 'asm-data',
            'hex' => 'hex-data',
        ]);

        $this->assertSame('asm-data', $dto->asm);
        $this->assertSame('hex-data', $dto->hex);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = ScriptSigData::fromRpc([]);

        $this->assertSame('', $dto->asm);
        $this->assertSame('', $dto->hex);
    }
}
