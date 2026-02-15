<?php

namespace Tests\Feature\Api;

use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MempoolFeeEstimatesTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_returns_fee_estimates_from_electrs(): void
    {
        $mockData = [
            '2' => 10.03,
            '6' => 5.2,
        ];

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getFeeEstimates')
            ->once()
            ->andReturn($mockData);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.mempool.fee-estimates'))
            ->assertOk()
            ->assertJson($mockData);
    }

    #[Test]
    public function it_falls_back_to_rpc_when_electrs_is_empty(): void
    {
        $rpcData = [
            '2' => 10.03,
            '144' => 1.0,
        ];

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getFeeEstimates')
            ->once()
            ->andReturn([]);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        $explorer->shouldReceive('getFeeEstimates')
            ->once()
            ->andReturn($rpcData);
        $this->app->instance(PepecoinExplorerService::class, $explorer);

        $this->get(route('api.mempool.fee-estimates'))
            ->assertOk()
            ->assertJson($rpcData);
    }
}
