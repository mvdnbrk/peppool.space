<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\RawTransactionVerboseData;
use App\Data\Rpc\RawVinData;
use App\Data\Rpc\RawVoutData;
use PHPUnit\Framework\TestCase;

final class RawTransactionVerboseDataTest extends TestCase
{
    public function test_from_rpc_maps_fields_with_nested_vin_vout(): void
    {
        $dto = RawTransactionVerboseData::fromRpc([
            'hex' => '001122',
            'txid' => 'abcd',
            'hash' => 'efgh',
            'size' => 200,
            'vsize' => 150,
            'version' => 2,
            'locktime' => 0,
            'vin' => [
                [
                    'txid' => 'in1',
                    'vout' => 0,
                    'scriptSig' => ['asm' => 'asm1', 'hex' => 'hex1'],
                    'sequence' => 1,
                ],
            ],
            'vout' => [
                [
                    'value' => 1.23,
                    'n' => 0,
                    'scriptPubKey' => [
                        'asm' => 'asm2',
                        'hex' => 'hex2',
                        'reqSigs' => 1,
                        'type' => 'pubkeyhash',
                        'addresses' => ['Pabc'],
                    ],
                ],
            ],
            'blockhash' => 'blockhash',
            'confirmations' => 3,
            'time' => 123,
            'blocktime' => 456,
        ]);

        $this->assertSame('001122', $dto->hex);
        $this->assertSame('abcd', $dto->txid);
        $this->assertSame('efgh', $dto->hash);
        $this->assertSame(200, $dto->size);
        $this->assertSame(150, $dto->vsize);
        $this->assertSame(2, $dto->version);
        $this->assertSame(0, $dto->locktime);
        $this->assertCount(1, $dto->vin);
        $this->assertInstanceOf(RawVinData::class, $dto->vin[0]);
        $this->assertCount(1, $dto->vout);
        $this->assertInstanceOf(RawVoutData::class, $dto->vout[0]);
        $this->assertSame('blockhash', $dto->blockhash);
        $this->assertSame(3, $dto->confirmations);
        $this->assertSame(123, $dto->time);
        $this->assertSame(456, $dto->blocktime);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = RawTransactionVerboseData::fromRpc(['size' => 99]);

        $this->assertSame('', $dto->hex);
        $this->assertSame('', $dto->txid);
        $this->assertSame('', $dto->hash);
        $this->assertSame(99, $dto->size);
        $this->assertSame(99, $dto->vsize);
        $this->assertSame(0, $dto->version);
        $this->assertSame(0, $dto->locktime);
        $this->assertSame([], $dto->vin);
        $this->assertSame([], $dto->vout);
        $this->assertNull($dto->blockhash);
        $this->assertNull($dto->confirmations);
        $this->assertNull($dto->time);
        $this->assertNull($dto->blocktime);
    }
}
