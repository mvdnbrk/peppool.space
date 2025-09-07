<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Data\Rpc\NetworkInfoData;
use App\Data\Rpc\ValidateAddressData;
use App\Data\Rpc\TxOutSetInfoData;
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

        $this->assertSame(7, $service->getNetworkConnectionsCount());
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

    public function test_validate_address_returns_dto(): void
    {
        Cache::flush();

        $address = 'PValidAddress123';

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('call')
            ->once()
            ->with('validateaddress', [$address])
            ->andReturn([
                'isvalid' => true,
                'address' => $address,
                'scriptPubKey' => '76a914...88ac',
                'isscript' => false,
                'iswatchonly' => false,
            ]);

        $service = new PepecoinExplorerService($rpc);

        $dto = $service->validateAddress($address);

        $this->assertInstanceOf(ValidateAddressData::class, $dto);
        $this->assertTrue($dto->isValid);
        $this->assertSame($address, $dto->address);
        $this->assertSame('76a914...88ac', $dto->scriptPubKey);
        $this->assertFalse($dto->isScript);
        $this->assertFalse($dto->isWatchOnly);
    }

    public function test_validate_address_is_cached(): void
    {
        Cache::flush();

        $address = 'PCacheTestAddress';

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('call')
            ->once() // ensure only one RPC call due to caching
            ->with('validateaddress', [$address])
            ->andReturn([
                'isvalid' => true,
                'address' => $address,
            ]);

        $service = new PepecoinExplorerService($rpc);

        $first = $service->validateAddress($address);
        $second = $service->validateAddress($address);

        $this->assertSame($first->address, $second->address);
        $this->assertTrue($first->isValid);
        $this->assertTrue($second->isValid);
    }

    public function test_get_txoutsetinfo_returns_dto(): void
    {
        Cache::flush();

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('getTxOutSetInfo')
            ->once()
            ->andReturn([
                'height' => 123,
                'bestblock' => '0000abc',
                'transactions' => 456,
                'txouts' => 789,
                'bytes_serialized' => 1024,
                'hash_serialized' => 'deadbeef',
                'total_amount' => 123.45,
            ]);

        $service = new PepecoinExplorerService($rpc);

        $dto = $service->getTxOutSetInfoData();

        $this->assertInstanceOf(TxOutSetInfoData::class, $dto);
        $this->assertSame(123, $dto->height);
        $this->assertSame('0000abc', $dto->bestblock);
        $this->assertSame(456, $dto->transactions);
        $this->assertSame(789, $dto->txouts);
        $this->assertSame(1024, $dto->bytesSerialized);
        $this->assertSame('deadbeef', $dto->hashSerialized);
        $this->assertSame(123.45, $dto->totalAmount);
    }
}
