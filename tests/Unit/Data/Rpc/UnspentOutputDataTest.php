<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\UnspentOutputData;
use Tests\TestCase;

final class UnspentOutputDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = UnspentOutputData::from([
            'txid' => 'abcd',
            'vout' => 1,
            'address' => 'Pxyz',
            'scriptPubKey' => '76a9',
            'amount' => 12.34,
            'confirmations' => 5,
            'spendable' => true,
        ]);

        $this->assertSame('abcd', $dto->txid);
        $this->assertSame(1, $dto->vout);
        $this->assertSame('Pxyz', $dto->address);
        $this->assertSame('76a9', $dto->scriptPubKey);
        $this->assertSame(12.34, $dto->amount);
        $this->assertSame(5, $dto->confirmations);
        $this->assertTrue($dto->spendable);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = UnspentOutputData::from([]);

        $this->assertSame('', $dto->txid);
        $this->assertSame(0, $dto->vout);
        $this->assertNull($dto->address);
        $this->assertNull($dto->scriptPubKey);
        $this->assertSame(0.0, $dto->amount);
        $this->assertSame(0, $dto->confirmations);
        $this->assertFalse($dto->spendable);
    }
}
