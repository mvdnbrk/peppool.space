<?php

namespace Tests\Feature\Api;

use App\Data\Ordinals\OutputData;
use App\Services\OrdinalsService;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OutputShowTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_returns_output_data(): void
    {
        $txid = str_repeat('a', 64);
        $outpoint = "{$txid}:0";

        $mockData = OutputData::from([
            'address' => 'PFakeAddressForTestingPurposesOnly1',
            'confirmations' => 12,
            'indexed' => true,
            'inscriptions' => [],
            'outpoint' => $outpoint,
            'script_pubkey' => '76a914...88ac',
            'spent' => false,
            'transaction' => $txid,
            'value' => 100000,
        ]);

        $ordinals = Mockery::mock(OrdinalsService::class);
        $ordinals->shouldReceive('getOutput')
            ->once()
            ->with($outpoint)
            ->andReturn($mockData);
        $this->app->instance(OrdinalsService::class, $ordinals);

        $this->get(route('api.output.show', ['outpoint' => $outpoint]))
            ->assertOk()
            ->assertJson([
                'address' => 'PFakeAddressForTestingPurposesOnly1',
                'confirmations' => 12,
                'indexed' => true,
                'outpoint' => $outpoint,
                'script_pubkey' => '76a914...88ac',
                'spent' => false,
                'transaction' => $txid,
                'value' => 100000,
            ]);
    }

    #[Test]
    public function it_returns_error_for_invalid_outpoint(): void
    {
        $this->get('/api/output/invalid-outpoint')
            ->assertStatus(400)
            ->assertJson([
                'code' => 400,
                'error' => 'invalid_outpoint',
                'message' => 'The provided outpoint is invalid. Expected format: <txid>:<vout>',
            ]);
    }

    #[Test]
    public function it_returns_not_found_when_output_is_not_indexed(): void
    {
        $txid = str_repeat('b', 64);
        $outpoint = "{$txid}:0";

        $stub = OutputData::from([
            'address' => null,
            'confirmations' => 0,
            'indexed' => false,
            'inscriptions' => [],
            'outpoint' => $outpoint,
            'script_pubkey' => '',
            'spent' => true,
            'transaction' => $txid,
            'value' => 0,
        ]);

        $ordinals = Mockery::mock(OrdinalsService::class);
        $ordinals->shouldReceive('getOutput')
            ->once()
            ->with($outpoint)
            ->andReturn($stub);
        $this->app->instance(OrdinalsService::class, $ordinals);

        $this->get(route('api.output.show', ['outpoint' => $outpoint]))
            ->assertStatus(404)
            ->assertJson([
                'code' => 404,
                'error' => 'output_not_found',
                'message' => 'The requested output could not be found.',
            ]);
    }

    #[Test]
    public function it_returns_error_when_output_is_not_found(): void
    {
        $outpoint = '0000000000000000000000000000000000000000000000000000000000000000:0';

        $response404 = new Response(new GuzzleResponse(404));
        $exception404 = new RequestException($response404);

        $ordinals = Mockery::mock(OrdinalsService::class);
        $ordinals->shouldReceive('getOutput')
            ->once()
            ->with($outpoint)
            ->andThrow($exception404);
        $this->app->instance(OrdinalsService::class, $ordinals);

        $this->get(route('api.output.show', ['outpoint' => $outpoint]))
            ->assertStatus(404)
            ->assertJson([
                'code' => 404,
                'error' => 'output_not_found',
                'message' => 'The requested output could not be found.',
            ]);
    }
}
