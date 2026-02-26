<?php

namespace Tests\Feature\Api;

use App\Contracts\BlockchainServiceInterface;
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
    public function it_returns_fee_estimates(): void
    {
        $mockData = [
            '2' => 10.03,
            '6' => 5.2,
        ];

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getFeeEstimates')
            ->once()
            ->andReturn($mockData);
        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('api.mempool.fee-estimates'))
            ->assertOk()
            ->assertJson($mockData);
    }
}
