<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\RawVinData;
use App\Data\Rpc\ScriptSigData;
use PHPUnit\Framework\TestCase;

final class RawVinDataTest extends TestCase
{
    public function test_from_rpc_maps_fields_with_script_sig(): void
    {
        $dto = RawVinData::fromRpc([
            'txid' => 'abcd',
            'vout' => 1,
            'scriptSig' => [
                'asm' => 'OP_DUP',
                'hex' => '76a9',
            ],
            'sequence' => 42,
            'coinbase' => null,
        ]);

        $this->assertSame('abcd', $dto->txid);
        $this->assertSame(1, $dto->vout);
        $this->assertInstanceOf(ScriptSigData::class, $dto->scriptSig);
        $this->assertSame('OP_DUP', $dto->scriptSig->asm);
        $this->assertSame('76a9', $dto->scriptSig->hex);
        $this->assertSame(42, $dto->sequence);
        $this->assertNull($dto->coinbase);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = RawVinData::fromRpc([]);

        $this->assertNull($dto->txid);
        $this->assertNull($dto->vout);
        $this->assertNull($dto->scriptSig);
        $this->assertNull($dto->sequence);
        $this->assertNull($dto->coinbase);
    }
}
