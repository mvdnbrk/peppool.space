<?php

namespace Tests\Feature\Api;

use App\Data\Ordinals\InscriptionData;
use App\Services\OrdinalsService;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InscriptionShowTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_returns_inscription_data(): void
    {
        $inscriptionId = '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3i0';

        $mockData = InscriptionData::from([
            'id' => $inscriptionId,
            'number' => 17212333,
            'address' => 'Pvkk9bUW8S4AK4cJeDDebnWJNADNCtxCHG',
            'child_count' => 0,
            'children' => [],
            'content_type' => 'image/png',
            'effective_content_type' => 'image/png',
            'content_length' => 793,
            'delegate' => null,
            'fee' => 10210000,
            'height' => 956437,
            'value' => 100000,
            'parent_count' => 0,
            'parents' => [],
            'properties' => null,
            'satpoint' => '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3:0:0',
            'timestamp' => 1773570237,
            'next' => '1a560cb5a7215318a6856104128a9131291f4c737ce84da7abe6f00c33492bc5i0',
            'previous' => '8cc1647be4618dfd79ff29a0867f8426d9aa63f58ba6e154b111f12e6b099df9i0',
        ]);

        $ordinals = Mockery::mock(OrdinalsService::class);
        $ordinals->shouldReceive('getInscription')
            ->once()
            ->with($inscriptionId)
            ->andReturn($mockData);
        $this->app->instance(OrdinalsService::class, $ordinals);

        $this->get(route('api.inscription.show', ['inscriptionId' => $inscriptionId]))
            ->assertOk()
            ->assertJson([
                'id' => $inscriptionId,
                'number' => 17212333,
                'address' => 'Pvkk9bUW8S4AK4cJeDDebnWJNADNCtxCHG',
                'content_type' => 'image/png',
                'content_length' => 793,
            ]);
    }

    #[Test]
    public function it_returns_404_for_invalid_inscription_id(): void
    {
        $this->get('/api/inscription/invalid-id')
            ->assertStatus(404);
    }

    #[Test]
    public function it_returns_error_when_inscription_is_not_found(): void
    {
        $inscriptionId = '0000000000000000000000000000000000000000000000000000000000000000i0';

        $response404 = new Response(new GuzzleResponse(404));
        $exception404 = new RequestException($response404);

        $ordinals = Mockery::mock(OrdinalsService::class);
        $ordinals->shouldReceive('getInscription')
            ->once()
            ->with($inscriptionId)
            ->andThrow($exception404);
        $this->app->instance(OrdinalsService::class, $ordinals);

        $this->get(route('api.inscription.show', ['inscriptionId' => $inscriptionId]))
            ->assertStatus(404)
            ->assertJson([
                'code' => 404,
                'error' => 'inscription_not_found',
                'message' => 'The requested inscription could not be found.',
            ]);
    }
}
