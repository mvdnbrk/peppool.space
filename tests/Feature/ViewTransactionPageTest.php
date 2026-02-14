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
        $txid = '54b0af0a480e4ad9e650ab89867bba465a33ab37bc8681b28fbd598ad7799c42';

        $txData = TransactionData::from([
            'txid' => $txid,
            'version' => 1,
            'locktime' => 0,
            'size' => 225,
            'weight' => 900,
            'fee' => 1000000,
            'status' => [
                'confirmed' => true,
                'block_height' => 915194,
                'block_hash' => 'some-block-hash',
                'block_time' => 1700000000,
            ],
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
                    'scriptpubkey' => 'some-script',
                    'scriptpubkey_address' => 'address-2',
                ],
            ],
        ]);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getTransaction')
            ->once()
            ->with($txid)
            ->andReturn($txData);

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        $explorer->shouldReceive('getBlockTipHeight')
            ->andReturn(915194);

        $this->app->instance(ElectrsPepeService::class, $electrs);
        $this->app->instance(PepecoinExplorerService::class, $explorer);

        $this->get(route('transaction.show', ['txid' => $txid]))
            ->assertOk()
            ->assertSee('Transaction Details')
            ->assertSee($txid)
            ->assertSee('Confirmed')
            ->assertSee('5.00')
            ->assertSee('4.99')
            ->assertSee('0.01');
    }

    #[Test]
    public function transaction_page_renders_unconfirmed_transaction(): void
    {
        $txid = '54b0af0a480e4ad9e650ab89867bba465a33ab37bc8681b28fbd598ad7799c42';

        $txData = TransactionData::from([
            'txid' => $txid,
            'version' => 1,
            'locktime' => 0,
            'size' => 225,
            'weight' => 900,
            'fee' => 1000000,
            'status' => [
                'confirmed' => false,
            ],
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
                    'scriptpubkey' => 'some-script',
                    'scriptpubkey_address' => 'address-2',
                ],
            ],
        ]);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getTransaction')
            ->once()
            ->with($txid)
            ->andReturn($txData);

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        $explorer->shouldReceive('getMempoolEntryTime')
            ->with($txid)
            ->andReturn(1700000000);

        $this->app->instance(ElectrsPepeService::class, $electrs);
        $this->app->instance(PepecoinExplorerService::class, $explorer);

        $this->get(route('transaction.show', ['txid' => $txid]))
            ->assertOk()
            ->assertSee('Unconfirmed');
    }
}
