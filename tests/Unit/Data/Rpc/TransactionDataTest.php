<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\TransactionData;
use Tests\TestCase;

final class TransactionDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = TransactionData::from([
            'txid' => 'abcd',
            'version' => 2,
            'size' => 225,
            'vsize' => 150,
            'locktime' => 0,
            'time' => 123456,
            'blocktime' => 123460,
            'blockhash' => 'deadbeef',
        ]);

        $this->assertSame('abcd', $dto->txid);
        $this->assertSame(2, $dto->version);
        $this->assertSame(225, $dto->size);
        $this->assertSame(150, $dto->vsize);
        $this->assertSame(0, $dto->locktime);
        $this->assertSame(123456, $dto->time);
        $this->assertSame(123460, $dto->blocktime);
        $this->assertSame('deadbeef', $dto->blockhash);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = TransactionData::from(['size' => 88]);

        $this->assertSame('', $dto->txid);
        $this->assertSame(0, $dto->version);
        $this->assertSame(88, $dto->size);
        $this->assertSame(88, $dto->vsize); // defaults to size when vsize missing
        $this->assertSame(0, $dto->locktime);
        $this->assertNull($dto->time);
        $this->assertNull($dto->blocktime);
        $this->assertNull($dto->blockhash);
    }
}
