<?php

namespace Tests\Feature\Api;

use App\Data\Electrs\AddressData;
use App\Services\ElectrsPepeService;
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
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.address.show', ['address' => $address]))
            ->assertStatus(400)
            ->assertJson([
                'error' => 'invalid_address',
                'message' => 'The provided address is invalid.',
                'code' => 400,
            ]);
    }
}
