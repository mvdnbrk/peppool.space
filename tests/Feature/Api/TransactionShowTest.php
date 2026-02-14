<?php

namespace Tests\Feature\Api;

use App\Data\Electrs\TransactionData;
use App\Services\ElectrsPepeService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionShowTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_returns_transaction_data(): void
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
                'confirmed' => true,
                'block_height' => 936511,
                'block_hash' => 'some-hash',
                'block_time' => 1771054926,
            ],
        ]);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getTransaction')
            ->once()
            ->with($txid)
            ->andReturn($mockData);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.tx.show', ['txid' => $txid]))
            ->assertOk()
            ->assertJson([
                'txid' => $txid,
                'version' => 1,
                'locktime' => 0,
                'size' => 221,
                'weight' => 557,
                'fee' => 19,
                'status' => [
                    'confirmed' => true,
                    'block_height' => 936511,
                    'block_hash' => 'some-hash',
                    'block_time' => 1771054926,
                ],
            ]);
    }

    #[Test]
    public function it_returns_error_for_invalid_hex_string(): void
    {
        $invalidTxid = 'too-short';

        $this->get(route('api.tx.show', ['txid' => $invalidTxid]))
            ->assertStatus(400)
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Invalid hex string');

        $this->get(route('api.tx.status', ['txid' => $invalidTxid]))
            ->assertStatus(400)
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Invalid hex string');
    }

    #[Test]
    public function it_returns_error_when_transaction_is_not_found(): void
    {
        $txid = '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c260';

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getTransaction')
            ->twice() // once for show, once for status
            ->with($txid)
            ->andThrow(new \Illuminate\Http\Client\RequestException(
                new \Illuminate\Http\Client\Response(
                    new \GuzzleHttp\Psr7\Response(404)
                )
            ));
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.tx.show', ['txid' => $txid]))
            ->assertStatus(404)
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Transaction not found');

        $this->get(route('api.tx.status', ['txid' => $txid]))
            ->assertStatus(404)
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Transaction not found');
    }

    #[Test]
    public function it_returns_confirmed_transaction_status(): void
    {
        $txid = '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261';

        $mockData = TransactionData::from([
            'txid' => $txid,
            'version' => 1,
            'locktime' => 0,
            'vin' => [],
            'vout' => [],
            'size' => 221,
            'weight' => 557,
            'fee' => 19,
            'status' => [
                'confirmed' => true,
                'block_height' => 936511,
                'block_hash' => 'some-hash',
                'block_time' => 1771054926,
            ],
        ]);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getTransaction')
            ->once()
            ->with($txid)
            ->andReturn($mockData);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.tx.status', ['txid' => $txid]))
            ->assertOk()
            ->assertExactJson([
                'confirmed' => true,
                'block_height' => 936511,
                'block_hash' => 'some-hash',
                'block_time' => 1771054926,
            ]);
    }

    #[Test]
    public function it_returns_unconfirmed_transaction_status(): void
    {
        $txid = '7d8c6b9f05301e592bc160531787631a420e056bb64f9d88ab8e4ceb12906b02';

        $mockData = TransactionData::from([
            'txid' => $txid,
            'version' => 1,
            'locktime' => 0,
            'vin' => [],
            'vout' => [],
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
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.tx.status', ['txid' => $txid]))
            ->assertOk()
            ->assertExactJson([
                'confirmed' => false,
            ]);
    }

    #[Test]
    public function it_returns_transaction_hex(): void
    {
        $txid = '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261';
        $hex = '010000000536a007284bd52ee826680a7f43536472f1bcce1e76cd76b826b88c5884eddf1f0c0000006b483045022100bcdf40fb3b5ebfa2c158ac8d1a41c03eb3dba4e180b00e81836bafd56d946efd022005cc40e35022b614275c1e485c409599667cbd41f6e5d78f421cb260a020a24f01210255ea3f53ce3ed1ad2c08dfc23b211b15b852afb819492a9a0f3f99e5747cb5f0ffffffffee08cb90c4e84dd7952b2cfad81ed3b088f5b';

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getRawTransaction')
            ->once()
            ->with($txid)
            ->andReturn($hex);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.tx.hex', ['txid' => $txid]))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee($hex);
    }

    #[Test]
    public function it_returns_transaction_raw_binary(): void
    {
        $txid = '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261';
        $hex = '01020304'; // Short hex for testing
        $binary = hex2bin($hex);

        $electrs = Mockery::mock(ElectrsPepeService::class);
        $electrs->shouldReceive('getRawTransaction')
            ->once()
            ->with($txid)
            ->andReturn($hex);
        $this->app->instance(ElectrsPepeService::class, $electrs);

        $this->get(route('api.tx.raw', ['txid' => $txid]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/octet-stream')
            ->assertSee($binary);
    }
}
