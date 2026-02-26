<?php

namespace Tests\Feature\Api;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\NetworkInfoData;
use App\Services\PepecoinRpcService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FeesTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_returns_recommended_fees(): void
    {
        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('getNetworkInfo')->andReturn(NetworkInfoData::from(['relayfee' => 0.001]));
        $this->app->instance(PepecoinRpcService::class, $rpc);

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getFeeEstimates')->andReturn([
            '1' => 10.0,
            '6' => 8.0,
            '12' => 5.0,
            '144' => 2.0,
        ]);
        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('api.fees.recommended'))
            ->assertOk()
            ->assertJson([
                'fastestFee' => 10,
                'halfHourFee' => 8,
                'hourFee' => 5,
                'economyFee' => 2,
                'minimumFee' => 1,
            ]);
    }

    #[Test]
    public function it_returns_precise_fees(): void
    {
        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('getNetworkInfo')->andReturn(NetworkInfoData::from(['relayfee' => 0.001]));
        $this->app->instance(PepecoinRpcService::class, $rpc);

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getFeeEstimates')->andReturn([
            '1' => 10.5,
        ]);
        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('api.fees.precise'))
            ->assertOk()
            ->assertJson([
                'fastestFee' => 10.5,
                'minimumFee' => 1.0,
            ]);
    }
}
