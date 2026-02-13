<?php

namespace Tests\Feature\Api;

use App\Data\Electrs\MempoolData;
use App\Data\Rpc\MempoolInfoData;
use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MempoolIndexTest extends TestCase
{
    #[Test]
    public function mempool_index_returns_data_from_electrs(): void
    {
        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getMempool')->once()->andReturn(new MempoolData(
            count: 12,
            vsize: 3904,
            totalFee: 24200000
        ));
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.mempool.index'))
            ->assertOk()
            ->assertJson([
                'count' => 12,
                'vsize' => 3904,
                'total_fee' => 0.242,
            ]);
    }

    #[Test]
    public function mempool_index_falls_back_to_rpc_on_electrs_failure(): void
    {
        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getMempool')->once()->andThrow(new \Exception('Electrs down'));
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $rpc = Mockery::mock(PepecoinExplorerService::class);
        $rpc->shouldReceive('getMempoolInfo')->once()->andReturn(new MempoolInfoData(
            size: 123,
            bytes: 4567
        ));
        $this->app->instance(PepecoinExplorerService::class, $rpc);

        $this->get(route('api.mempool.index'))
            ->assertOk()
            ->assertJson([
                'count' => 123,
                'bytes' => 4567,
            ]);
    }
}
