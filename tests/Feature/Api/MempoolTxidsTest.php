<?php

namespace Tests\Feature\Api;

use App\Services\PepecoinExplorerService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MempoolTxidsTest extends TestCase
{
    #[Test]
    public function test_mempool_txids_returns_array_of_txids(): void
    {
        $mock = Mockery::mock(PepecoinExplorerService::class);
        $mock->shouldReceive('getMempoolTxIds')->once()->andReturn(collect([
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
        ]));
        $this->app->instance(PepecoinExplorerService::class, $mock);

        $this->get(route('api.mempool.txids'))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            ]);
    }
}
