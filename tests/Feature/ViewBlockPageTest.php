<?php

namespace Tests\Feature;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\BlockData;
use App\Data\Blockchain\TransactionData;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewBlockPageTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function block_page_renders_correctly(): void
    {
        $height = 696874;
        $hash = 'e06fe3340f9afd0bf9675a498eeacb31530fd3afede31d9b110f10153e8715aa';

        $blockData = BlockData::from([
            'id' => $hash,
            'height' => $height,
            'version' => 1,
            'timestamp' => 1757323663,
            'tx_count' => 1,
            'size' => 1000,
            'weight' => 4000,
            'merkle_root' => 'root',
            'previousblockhash' => 'prev-hash',
            'mediantime' => 1757323663,
            'nonce' => 123,
            'bits' => 123,
            'difficulty' => 78004722.82,
        ]);

        $txData = TransactionData::from([
            'txid' => 'tx1',
            'version' => 1,
            'locktime' => 0,
            'size' => 225,
            'weight' => 900,
            'fee' => 0,
            'status' => ['confirmed' => true],
            'vin' => [['is_coinbase' => true]],
            'vout' => [['value' => 100000000, 'scriptpubkey' => 'abc']],
        ]);

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getBlockHash')
            ->with($height)
            ->andReturn($hash);
        $blockchain->shouldReceive('getBlock')
            ->with($hash)
            ->once()
            ->andReturn($blockData);
        $blockchain->shouldReceive('getBlockTransactions')
            ->with($hash)
            ->once()
            ->andReturn(collect([$txData]));
        $blockchain->shouldReceive('getBlockTipHeight')
            ->andReturn($height + 1);

        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('block.show', ['hashOrHeight' => $height]))
            ->assertOk()
            ->assertSee('Block 696,874')
            ->assertSee($hash)
            ->assertSee('78.00 M') // Formatted difficulty
            ->assertSee('1,000 bytes')
            ->assertSee('1.00'); // Transaction amount
    }
}
