<?php

namespace Tests\Feature\Services;

use App\Data\Electrs\AddressData;
use App\Services\ElectrsPepeService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ElectrsPepeServiceTest extends TestCase
{
    public function test_it_can_fetch_address_data(): void
    {
        $address = 'PbPiW62L4zUeSNCmKxBaMqKcVGB4t5JTD2';
        $mockResponse = [
            'address' => $address,
            'chain_stats' => [
                'funded_txo_count' => 1,
                'funded_txo_sum' => 1029372096000,
                'spent_txo_count' => 1,
                'spent_txo_sum' => 1029372096000,
                'tx_count' => 2,
            ],
            'mempool_stats' => [
                'funded_txo_count' => 0,
                'funded_txo_sum' => 0,
                'spent_txo_count' => 0,
                'spent_txo_sum' => 0,
                'tx_count' => 0,
            ],
        ];

        Http::fake([
            "*/address/{$address}" => Http::response($mockResponse),
        ]);

        $service = new ElectrsPepeService('http://127.0.0.1:3002');
        $data = $service->getAddress($address);

        $this->assertInstanceOf(AddressData::class, $data);
        $this->assertEquals($address, $data->address);
        $this->assertEquals(0, $data->getConfirmedBalance());
        $this->assertEquals(10293.72096, $data->chainStats->getTotalReceived());
        $this->assertEquals(10293.72096, $data->chainStats->getTotalSent());
    }
}
