<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\NetworkInfoLocalAddressData;
use PHPUnit\Framework\TestCase;

final class NetworkInfoLocalAddressDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = NetworkInfoLocalAddressData::fromRpc([
            'address' => '127.0.0.1',
            'port' => 33874,
            'score' => 1,
        ]);

        $this->assertSame('127.0.0.1', $dto->address);
        $this->assertSame(33874, $dto->port);
        $this->assertSame(1, $dto->score);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = NetworkInfoLocalAddressData::fromRpc([]);

        $this->assertSame('', $dto->address);
        $this->assertSame(0, $dto->port);
        $this->assertSame(0, $dto->score);
    }
}
