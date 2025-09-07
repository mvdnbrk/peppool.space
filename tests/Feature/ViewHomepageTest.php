<?php

namespace Tests\Feature;

use App\Data\Rpc\BlockchainInfoData;
use App\Models\Block;
use App\Services\PepecoinExplorerService;
use App\Services\PepecoinRpcService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewHomepageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function homepage_can_be_viewed(): void
    {
        // Create some test blocks for Block::getLatestBlocks()
        Block::factory()->count(3)->create();

        // Mock RPC service
        $rpcMock = Mockery::mock(PepecoinRpcService::class);
        $rpcMock->shouldReceive('getBlockchainInfo')->andReturn(BlockchainInfoData::from([
            'chain' => 'main',
            'blocks' => 655982,
            'difficulty' => 1234.56,
        ]));
        $this->app->instance(PepecoinRpcService::class, $rpcMock);

        // Mock Explorer service
        $explorerMock = Mockery::mock(PepecoinExplorerService::class);
        $explorerMock->shouldReceive('getMempoolInfo')->andReturn(collect(['size' => 10, 'bytes' => 1000]));
        $explorerMock->shouldReceive('getNetworkSubversion')->andReturn('/pepetoshi:1.1.0/');
        $explorerMock->shouldReceive('getNetworkConnectionsCount')->andReturn(8);
        $explorerMock->shouldReceive('getMempoolTxIds')->andReturn(collect(['tx1', 'tx2']));
        $explorerMock->shouldReceive('getChainSize')->andReturn(1000000000);
        $explorerMock->shouldReceive('getBlockTipHeight')->andReturn(655982);
        $explorerMock->shouldReceive('getDifficulty')->andReturn(1234.56);
        $explorerMock->shouldReceive('getHashrate')->andReturn(123456789);
        $this->app->instance(PepecoinExplorerService::class, $explorerMock);

        $this->get('/')
            ->assertOk()
            ->assertSee('Pepecoin')
            ->assertSee('Latest Blocks')
            ->assertSee('Mempool Transactions')
            ->assertSee('real-time pepecoin blockchain explorer')
            ->assertSee('peppool.space');
    }
}
