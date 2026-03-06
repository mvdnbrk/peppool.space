<?php

namespace Tests\Feature\Api;

use App\Contracts\BlockchainServiceInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressHistoryLimitTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_returns_too_many_transactions_error_when_electrs_limits_history(): void
    {
        $address = 'PYRX81df4T6sBdzYEG4YJCuB3eZqYz2YzJ';

        $guzzleResponse = new GuzzleResponse(400, [], 'Too many history entries');
        $response = new Response($guzzleResponse);
        $exception = new RequestException($response);

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getAddressUtxos')
            ->once()
            ->with($address)
            ->andThrow($exception);

        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('api.address.utxo', ['address' => $address]))
            ->assertStatus(400)
            ->assertJson([
                'code' => 400,
                'error' => 'too_many_transactions',
                'message' => 'This address has too many transactions to be indexed by this server.',
            ]);
    }

    #[Test]
    public function it_returns_invalid_address_error_for_other_400_errors(): void
    {
        $address = 'some-address';

        $guzzleResponse = new GuzzleResponse(400, [], 'base58 error');
        $response = new Response($guzzleResponse);
        $exception = new RequestException($response);

        $blockchain = Mockery::mock(BlockchainServiceInterface::class);
        $blockchain->shouldReceive('getAddressUtxos')
            ->once()
            ->with($address)
            ->andThrow($exception);

        $this->app->instance(BlockchainServiceInterface::class, $blockchain);

        $this->get(route('api.address.utxo', ['address' => $address]))
            ->assertStatus(400)
            ->assertJson([
                'code' => 400,
                'error' => 'invalid_address',
                'message' => 'The provided address is invalid.',
            ]);
    }
}
