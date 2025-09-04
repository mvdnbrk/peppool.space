<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\TxOutSetInfoData;
use PHPUnit\Framework\TestCase;

final class TxOutSetInfoDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = TxOutSetInfoData::fromRpc([
            'height' => 1,
            'bestblock' => 'abcd',
            'transactions' => 10,
            'txouts' => 20,
            'bytes_serialized' => 1000,
            'hash_serialized' => 'deadbeef',
            'total_amount' => 123.45,
        ]);

        $this->assertSame(1, $dto->height);
        $this->assertSame('abcd', $dto->bestblock);
        $this->assertSame(10, $dto->transactions);
        $this->assertSame(20, $dto->txouts);
        $this->assertSame(1000, $dto->bytesSerialized);
        $this->assertSame('deadbeef', $dto->hashSerialized);
        $this->assertSame(123.45, $dto->totalAmount);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = TxOutSetInfoData::fromRpc([]);

        $this->assertSame(0, $dto->height);
        $this->assertSame('', $dto->bestblock);
        $this->assertSame(0, $dto->transactions);
        $this->assertSame(0, $dto->txouts);
        $this->assertSame(0, $dto->bytesSerialized);
        $this->assertSame('', $dto->hashSerialized);
        $this->assertSame(0.0, $dto->totalAmount);
    }
}
