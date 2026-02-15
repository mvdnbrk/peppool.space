<?php

namespace Tests\Feature\Api;

use App\Data\Rpc\NetworkInfoData;
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
        $rpc->shouldReceive('call')->with('estimatesmartfee', [1])->andReturn(['feerate' => 0.01]);
        $rpc->shouldReceive('call')->with('estimatesmartfee', [6])->andReturn(['feerate' => 0.008]);
        $rpc->shouldReceive('call')->with('estimatesmartfee', [12])->andReturn(['feerate' => 0.005]);
        $rpc->shouldReceive('call')->with('estimatesmartfee', [144])->andReturn(['feerate' => 0.002]);
        // Also mock other targets from the list to avoid unexpected calls
        $rpc->shouldReceive('call')->zeroOrMoreTimes()->andReturn([]);

        $this->app->instance(PepecoinRpcService::class, $rpc);

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
        $rpc->shouldReceive('call')->with('estimatesmartfee', [1])->andReturn(['feerate' => 0.0105]);
        $rpc->shouldReceive('call')->zeroOrMoreTimes()->andReturn([]);

        $this->app->instance(PepecoinRpcService::class, $rpc);

        $this->get(route('api.fees.precise'))
            ->assertOk()
            ->assertJson([
                'fastestFee' => 10.5,
                'minimumFee' => 1.0,
            ]);
    }
}
