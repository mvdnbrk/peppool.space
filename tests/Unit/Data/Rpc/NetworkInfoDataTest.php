<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\NetworkInfoData;
use App\Data\Rpc\NetworkInfoLocalAddressData;
use App\Data\Rpc\NetworkInfoNetworkData;
use PHPUnit\Framework\TestCase;

final class NetworkInfoDataTest extends TestCase
{
    public function test_from_rpc_maps_fields_with_nested_arrays(): void
    {
        $dto = NetworkInfoData::fromRpc([
            'version' => 170000,
            'subversion' => '/pepetoshi:1.1.0/',
            'protocolversion' => 70016,
            'localservices' => '0000000000000000',
            'localrelay' => true,
            'timeoffset' => 0,
            'networkactive' => true,
            'connections' => 8,
            'networks' => [
                ['name' => 'ipv4', 'limited' => false, 'reachable' => true, 'proxy' => '', 'proxy_randomize_credentials' => false],
            ],
            'relayfee' => 0.0,
            'incrementalfee' => 0.0,
            'softdustlimit' => 0.0,
            'harddustlimit' => 0.0,
            'localaddresses' => [
                ['address' => '127.0.0.1', 'port' => 33874, 'score' => 1],
            ],
            'warnings' => '',
        ]);

        $this->assertSame(170000, $dto->version);
        $this->assertSame('/pepetoshi:1.1.0/', $dto->subversion);
        $this->assertSame(70016, $dto->protocolVersion);
        $this->assertSame('0000000000000000', $dto->localServices);
        $this->assertTrue($dto->localRelay);
        $this->assertSame(0, $dto->timeOffset);
        $this->assertTrue($dto->networkActive);
        $this->assertSame(8, $dto->connections);
        $this->assertCount(1, $dto->networks);
        $this->assertInstanceOf(NetworkInfoNetworkData::class, $dto->networks[0]);
        $this->assertSame(0.0, $dto->relayFee);
        $this->assertSame(0.0, $dto->incrementalFee);
        $this->assertSame(0.0, $dto->softDustLimit);
        $this->assertSame(0.0, $dto->hardDustLimit);
        $this->assertCount(1, $dto->localAddresses);
        $this->assertInstanceOf(NetworkInfoLocalAddressData::class, $dto->localAddresses[0]);
        $this->assertSame('', $dto->warnings);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = NetworkInfoData::fromRpc([]);

        $this->assertSame(0, $dto->version);
        $this->assertSame('', $dto->subversion);
        $this->assertSame(0, $dto->protocolVersion);
        $this->assertSame('', $dto->localServices);
        $this->assertFalse($dto->localRelay);
        $this->assertSame(0, $dto->timeOffset);
        $this->assertFalse($dto->networkActive);
        $this->assertSame(0, $dto->connections);
        $this->assertSame([], $dto->networks);
        $this->assertSame(0.0, $dto->relayFee);
        $this->assertSame(0.0, $dto->incrementalFee);
        $this->assertSame(0.0, $dto->softDustLimit);
        $this->assertSame(0.0, $dto->hardDustLimit);
        $this->assertSame([], $dto->localAddresses);
        $this->assertSame('', $dto->warnings);
    }
}
