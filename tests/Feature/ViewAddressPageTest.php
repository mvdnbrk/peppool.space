<?php

namespace Tests\Feature;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\AddressData;
use App\Data\Blockchain\TransactionData;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewAddressPageTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function address_page_shows_correct_total_count_and_paginates(): void
    {
        $address = 'PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M';

        $mockData = AddressData::from([
            'address' => $address,
            'chain_stats' => [
                'funded_txo_count' => 10,
                'funded_txo_sum' => 1000000000,
                'spent_txo_count' => 0,
                'spent_txo_sum' => 0,
                'tx_count' => 100, // Total 100 txs
            ],
            'mempool_stats' => [
                'funded_txo_count' => 0,
                'funded_txo_sum' => 0,
                'spent_txo_count' => 0,
                'spent_txo_sum' => 0,
                'tx_count' => 0,
            ],
        ]);

        $txs = collect(range(1, 25))->map(fn ($i) => TransactionData::from([
            'txid' => "tx$i",
            'version' => 1,
            'locktime' => 0,
            'vin' => [['is_coinbase' => false, 'sequence' => 0]],
            'vout' => [],
            'size' => 100,
            'weight' => 400,
            'fee' => 1000,
            'status' => [
                'confirmed' => true,
                'block_height' => 900,
                'block_hash' => 'hash',
                'block_time' => 1700000000,
            ],
        ]));

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getAddress')
            ->once()
            ->with($address)
            ->andReturn($mockData);
        $blockchain->shouldReceive('getAddressTransactions')
            ->once()
            ->with($address, null)
            ->andReturn($txs);
        $blockchain->shouldReceive('getBlockTipHeight')
            ->andReturn(1000);

        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('address.show', ['address' => $address]))
            ->assertOk()
            ->assertSee('100') // Total tx count from stats
            ->assertSee('tx1');
    }

    #[Test]
    public function address_page_uses_cursor_for_page_2(): void
    {
        $address = 'PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M';
        $afterTxid = 'tx25';

        $mockData = AddressData::from([
            'address' => $address,
            'chain_stats' => [
                'funded_txo_count' => 10,
                'funded_txo_sum' => 1000000000,
                'spent_txo_count' => 0,
                'spent_txo_sum' => 0,
                'tx_count' => 100,
            ],
            'mempool_stats' => [
                'funded_txo_count' => 0,
                'funded_txo_sum' => 0,
                'spent_txo_count' => 0,
                'spent_txo_sum' => 0,
                'tx_count' => 0,
            ],
        ]);

        $txs = collect(range(26, 50))->map(fn ($i) => TransactionData::from([
            'txid' => "tx$i",
            'version' => 1,
            'locktime' => 0,
            'vin' => [['is_coinbase' => false, 'sequence' => 0]],
            'vout' => [],
            'size' => 100,
            'weight' => 400,
            'fee' => 1000,
            'status' => [
                'confirmed' => true,
                'block_height' => 900,
                'block_hash' => 'hash',
                'block_time' => 1700000000,
            ],
        ]));

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getAddress')
            ->once()
            ->with($address)
            ->andReturn($mockData);
        $blockchain->shouldReceive('getAddressTransactions')
            ->once()
            ->with($address, $afterTxid)
            ->andReturn($txs);
        $blockchain->shouldReceive('getBlockTipHeight')
            ->andReturn(1000);

        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('address.show', ['address' => $address, 'page' => 2, 'after' => $afterTxid, 'per_page' => 25]))
            ->assertOk()
            ->assertSee('tx26')
            ->assertDontSee('tx25');
    }
}
