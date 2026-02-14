<?php

namespace Tests\Feature;

use App\Data\Electrs\TransactionData;
use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewTransactionPageTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function transaction_page_renders_confirmed_transaction(): void
    {
        $txid = '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261';

        $mockData = TransactionData::from([
            'txid' => $txid,
            'version' => 1,
            'locktime' => 0,
            'vin' => [
                [
                    'txid' => 'prev-txid',
                    'vout' => 0,
                    'is_coinbase' => false,
                    'prevout' => [
                        'value' => 500000000,
                        'scriptpubkey_address' => 'address-1',
                    ],
                ],
            ],
            'vout' => [
                [
                    'value' => 499000000,
                    'scriptpubkey_address' => 'address-1',
                ],
            ],
            'size' => 221,
            'weight' => 557,
            'fee' => 1000000,
            'status' => [
                'confirmed' => true,
                'block_height' => 915965,
                'block_hash' => 'some-hash',
                'block_time' => 1771054926,
            ],
        ]);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getTransaction')
            ->once()
            ->with($txid)
            ->andReturn($mockData);

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        $explorer->shouldReceive('getBlockTipHeight')
            ->andReturn(915965);

        $this->app->instance(ElectrsPepeService::class, $electrs);
        $this->app->instance(PepecoinExplorerService::class, $explorer);

        $this->get(route('transaction.show', ['txid' => $txid]))
            ->assertOk()
            ->assertSee('data-vue="transaction-details"', false)
            ->assertSee($txid);
    }

    #[Test]
    public function transaction_page_renders_unconfirmed_transaction(): void
    {
        $txid = '7d8c6b9f05301e592bc160531787631a420e056bb64f9d88ab8e4ceb12906b02';

        $mockData = TransactionData::from([
            'txid' => $txid,
            'version' => 1,
            'locktime' => 0,
            'vin' => [
                [
                    'txid' => 'prev-txid',
                    'vout' => 0,
                    'is_coinbase' => false,
                    'prevout' => [
                        'value' => 673,
                        'scriptpubkey_address' => 'address-1',
                    ],
                ],
            ],
            'vout' => [
                [
                    'value' => 654,
                    'scriptpubkey_address' => 'address-1',
                ],
            ],
            'size' => 221,
            'weight' => 557,
            'fee' => 19,
            'status' => [
                'confirmed' => false,
            ],
        ]);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getTransaction')
            ->once()
            ->with($txid)
            ->andReturn($mockData);

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        $explorer->shouldReceive('getBlockTipHeight')
            ->andReturn(915965);
        $explorer->shouldReceive('getMempoolEntryTime')
            ->andReturn(1771054926);

        $this->app->instance(ElectrsPepeService::class, $electrs);
        $this->app->instance(PepecoinExplorerService::class, $explorer);

        $this->get(route('transaction.show', ['txid' => $txid]))
            ->assertOk()
            ->assertSee('data-vue="transaction-details"', false)
            ->assertSee($txid);
    }
}
