<?php

namespace Tests\Feature\Api;

use App\Data\Electrs\AddressData;
use App\Data\Electrs\TransactionData;
use App\Data\Electrs\UtxoData;
use App\Services\ElectrsPepeService;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressShowTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_returns_address_data(): void
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

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getAddress')
            ->once()
            ->with($address)
            ->andReturn($mockData);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.address.show', ['address' => $address]))
            ->assertOk()
            ->assertJson([
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
    }

    #[Test]
    public function it_returns_address_transactions(): void
    {
        $address = 'PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M';

        $mockData = TransactionData::collect([
            [
                'txid' => '58ed78527f8c2fc7e745d18c72978e6aaeb450b4816472a841d2d6453b6accb1',
                'version' => 1,
                'locktime' => 916695,
                'vin' => [],
                'vout' => [],
                'size' => 374,
                'weight' => 1496,
                'fee' => 374000,
                'status' => [
                    'confirmed' => true,
                    'block_height' => 916697,
                    'block_hash' => 'a991281771fb38bc5a0ac0b8a3872451c243fddd49116a3805a78a58c24620aa',
                    'block_time' => 1771080551,
                ],
            ],
        ], Collection::class);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getAddressTransactions')
            ->once()
            ->with($address)
            ->andReturn($mockData);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.address.transactions', ['address' => $address]))
            ->assertOk()
            ->assertJson([
                [
                    'txid' => '58ed78527f8c2fc7e745d18c72978e6aaeb450b4816472a841d2d6453b6accb1',
                    'status' => [
                        'confirmed' => true,
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_returns_address_utxos(): void
    {
        $address = 'PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M';

        $mockData = UtxoData::collect([
            [
                'txid' => '58ed78527f8c2fc7e745d18c72978e6aaeb450b4816472a841d2d6453b6accb1',
                'vout' => 0,
                'status' => [
                    'confirmed' => true,
                    'block_height' => 916697,
                    'block_hash' => 'a991281771fb38bc5a0ac0b8a3872451c243fddd49116a3805a78a58c24620aa',
                    'block_time' => 1771080551,
                ],
                'value' => 100000000,
            ],
        ], Collection::class);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getAddressUtxos')
            ->once()
            ->with($address)
            ->andReturn($mockData);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.address.utxo', ['address' => $address]))
            ->assertOk()
            ->assertJson([
                [
                    'txid' => '58ed78527f8c2fc7e745d18c72978e6aaeb450b4816472a841d2d6453b6accb1',
                    'vout' => 0,
                    'status' => [
                        'confirmed' => true,
                    ],
                    'value' => 100000000,
                ],
            ]);
    }

    #[Test]
    public function it_returns_error_for_invalid_address(): void
    {
        $address = 'invalid-address';

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getAddress')
            ->once()
            ->with($address)
            ->andThrow(new \Illuminate\Http\Client\RequestException(
                new \Illuminate\Http\Client\Response(
                    new \GuzzleHttp\Psr7\Response(400)
                )
            ));
        $electrs->shouldReceive('getAddressTransactions')
            ->once()
            ->with($address)
            ->andThrow(new \Illuminate\Http\Client\RequestException(
                new \Illuminate\Http\Client\Response(
                    new \GuzzleHttp\Psr7\Response(400)
                )
            ));
        $electrs->shouldReceive('getAddressUtxos')
            ->once()
            ->with($address)
            ->andThrow(new \Illuminate\Http\Client\RequestException(
                new \Illuminate\Http\Client\Response(
                    new \GuzzleHttp\Psr7\Response(400)
                )
            ));
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.address.show', ['address' => $address]))
            ->assertStatus(400)
            ->assertJson([
                'code' => 400,
                'error' => 'invalid_address',
                'message' => 'The provided address is invalid.',
            ]);

        $this->get(route('api.address.transactions', ['address' => $address]))
            ->assertStatus(400)
            ->assertJson([
                'code' => 400,
                'error' => 'invalid_address',
                'message' => 'The provided address is invalid.',
            ]);

        $this->get(route('api.address.utxo', ['address' => $address]))
            ->assertStatus(400)
            ->assertJson([
                'code' => 400,
                'error' => 'invalid_address',
                'message' => 'The provided address is invalid.',
            ]);
    }
}
