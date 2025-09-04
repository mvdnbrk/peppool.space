<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\NetworkInfoNetworkData;
use PHPUnit\Framework\TestCase;

final class NetworkInfoNetworkDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = NetworkInfoNetworkData::fromRpc([
            'name' => 'ipv4',
            'limited' => false,
            'reachable' => true,
            'proxy' => '',
            'proxy_randomize_credentials' => true,
        ]);

        $this->assertSame('ipv4', $dto->name);
        $this->assertFalse($dto->limited);
        $this->assertTrue($dto->reachable);
        $this->assertSame('', $dto->proxy);
        $this->assertTrue($dto->proxyRandomizeCredentials);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = NetworkInfoNetworkData::fromRpc([]);

        $this->assertSame('', $dto->name);
        $this->assertFalse($dto->limited);
        $this->assertFalse($dto->reachable);
        $this->assertSame('', $dto->proxy);
        $this->assertFalse($dto->proxyRandomizeCredentials);
    }
}
