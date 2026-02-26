<?php

namespace Tests\Feature;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\AddressData;
use App\Services\PepecoinExplorerService;
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
    public function address_page_renders_correctly(): void
    {
        $address = 'PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M';

        $mockData = AddressData::from([
            'address' => $address,
            'chain_stats' => [
                'funded_txo_count' => 1,
                'funded_txo_sum' => 100000000,
                'spent_txo_count' => 0,
                'spent_txo_sum' => 0,
                'tx_count' => 1,
            ],
            'mempool_stats' => [
                'funded_txo_count' => 0,
                'funded_txo_sum' => 0,
                'spent_txo_count' => 0,
                'spent_txo_sum' => 0,
                'tx_count' => 0,
            ],
        ]);

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getAddress')
            ->once()
            ->with($address)
            ->andReturn($mockData);
        $blockchain->shouldReceive('getAddressTransactions')
            ->once()
            ->with($address)
            ->andReturn(collect([]));

        $explorer = Mockery::mock(PepecoinExplorerService::class);

        $this->app->instance(BlockchainServiceInterface::class, $blockchain);
        $this->app->instance(PepecoinExplorerService::class, $explorer);

        $this->get(route('address.show', ['address' => $address]))
            ->assertOk()
            ->assertSee($address)
            ->assertSee('Address');
    }
}
