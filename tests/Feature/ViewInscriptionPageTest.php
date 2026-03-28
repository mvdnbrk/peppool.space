<?php

namespace Tests\Feature;

use App\Data\Ordinals\InscriptionData;
use App\Services\OrdinalsService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewInscriptionPageTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function inscription_page_renders_successfully(): void
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
        ]);

        $ordinals = Mockery::mock(OrdinalsService::class);
        $ordinals->shouldReceive('getInscription')
            ->once()
            ->with($inscriptionId)
            ->andReturn($mockData);
        $this->app->instance(OrdinalsService::class, $ordinals);

        $this->get(route('inscription.show', ['inscriptionId' => $inscriptionId]))
            ->assertOk()
            ->assertSee('Inscription #17,212,333')
            ->assertSee('Pvkk9bUW8S4AK4cJeDDebnWJNADNCtxCHG')
            ->assertSee('image/png');
    }

    #[Test]
    public function inscription_page_returns_404_when_not_found(): void
    {
        $inscriptionId = '0000000000000000000000000000000000000000000000000000000000000000i0';

        $ordinals = Mockery::mock(OrdinalsService::class);
        $ordinals->shouldReceive('getInscription')
            ->once()
            ->with($inscriptionId)
            ->andThrow(new \Illuminate\Http\Client\RequestException(
                new \Illuminate\Http\Client\Response(new \GuzzleHttp\Psr7\Response(404))
            ));
        $this->app->instance(OrdinalsService::class, $ordinals);

        $this->get(route('inscription.show', ['inscriptionId' => $inscriptionId]))
            ->assertNotFound();
    }
}
