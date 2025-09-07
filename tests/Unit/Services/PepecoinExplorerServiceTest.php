<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Data\Rpc\NetworkInfoData;
use App\Services\PepecoinExplorerService;
use App\Services\PepecoinRpcService;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

final class PepecoinExplorerServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_network_connections_reads_from_dto(): void
    {
        Cache::flush();

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('getNetworkInfo')
            ->once()
            ->andReturn(NetworkInfoData::from([
                'connections' => 7,
                'subversion' => '/pepetoshi:1.1.0/',
            ]));

        $service = new PepecoinExplorerService($rpc);

        $this->assertSame(7, $service->getNetworkConnections());
    }

    public function test_get_network_subversion_reads_from_dto(): void
    {
        Cache::flush();

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('getNetworkInfo')
            ->once()
            ->andReturn(NetworkInfoData::from([
                'connections' => 3,
                'subversion' => '/pepetoshi:1.1.0/',
            ]));

        $service = new PepecoinExplorerService($rpc);

        $this->assertSame('/pepetoshi:1.1.0/', $service->getNetworkSubversion());
    }
}
