<?php

namespace Tests\Feature\Api;

use App\Data\Rpc\MempoolInfoData;
use App\Services\PepecoinExplorerService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MempoolIndexTest extends TestCase
{
    #[Test]
    public function mempool_index_returns_count_and_bytes(): void
    {
        $mock = Mockery::mock(PepecoinExplorerService::class);
        $mock->shouldReceive('getMempoolInfo')->once()->andReturn(new MempoolInfoData(
            size: 123,
            bytes: 4567
        ));
        $this->app->instance(PepecoinExplorerService::class, $mock);

        $this->get(route('api.mempool.index'))
            ->assertOk()
            ->assertJson([
                'count' => 123,
                'bytes' => 4567,
            ]);
    }
}
