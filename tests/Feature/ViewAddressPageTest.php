<?php

namespace Tests\Feature;

use App\Data\Electrs\AddressData;
use App\Services\ElectrsPepeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewAddressPageTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function invalid_address_returns_404(): void
    {
        $this->get('/address/PEPEaddress1234567890ABCDEFGHIJ')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function valid_address_renders_correctly_using_electrs(): void
    {
        $address = 'PEPEaddress123456789ABCDEFGHiJ';

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getAddress')
            ->once()
            ->with($address)
            ->andReturn(AddressData::from([
                'address' => $address,
                'chain_stats' => [
                    'funded_txo_count' => 1,
                    'funded_txo_sum' => 100_000_000,
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
            ]));

        $electrs->shouldReceive('getAddressTransactions')
            ->once()
            ->with($address)
            ->andReturn([]);

        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('address.show', ['address' => $address]))
            ->assertOk()
            ->assertSee($address)
            ->assertSee('1.00'); // Balance
    }
}
