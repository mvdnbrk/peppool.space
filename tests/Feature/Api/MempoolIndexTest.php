<?php

namespace Tests\Feature\Api;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\MempoolData;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MempoolIndexTest extends TestCase
{
    #[Test]
    public function mempool_index_returns_data(): void
    {
        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getMempool')->once()->andReturn(new MempoolData(
            count: 12,
            vsize: 3904,
            totalFee: 24200000
        ));
        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('api.mempool.index'))
            ->assertOk()
            ->assertJson([
                'count' => 12,
                'vsize' => 3904,
                'total_fee' => 0.242,
            ]);
    }

    #[Test]
    public function mempool_index_handles_service_failure(): void
    {
        // The controller now just lets BlockchainService handle fallbacks.
        // If BlockchainServiceInterface throws, the controller lets it bubble up (or handles generic throwable).
        
        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getMempool')->once()->andThrow(new \Exception('Service down'));
        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('api.mempool.index'))
            ->assertStatus(500);
    }
}
