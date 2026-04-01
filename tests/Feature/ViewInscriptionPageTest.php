<?php

namespace Tests\Feature;

use App\Data\Ordinals\InscriptionData;
use App\Services\OrdinalsService;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\RequestException;
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
    public function inscription_page_shows_title_when_present(): void
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
            'properties' => [
                'title' => 'Sample Inscription Title',
            ],
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
            ->assertSee('Sample Inscription Title');
    }

    #[Test]
    public function inscription_page_shows_traits_when_present(): void
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
            'properties' => [
                'traits' => [
                    'eyes' => 'laser',
                    'tribe' => 'cat',
                ],
            ],
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
            ->assertSee('Traits')
            ->assertSee('eyes')
            ->assertSee('laser')
            ->assertSee('tribe')
            ->assertSee('cat');
    }

    #[Test]
    public function inscription_page_shows_parents_when_present(): void
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
            'parent_count' => 1,
            'parents' => ['parent_id_123'],
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
            ->assertSee('Parent Inscription')
            ->assertSee('parent_id_123');
    }

    #[Test]
    public function inscription_page_shows_children_when_present(): void
    {
        $inscriptionId = '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3i0';

        $mockData = InscriptionData::from([
            'id' => $inscriptionId,
            'number' => 17212333,
            'address' => 'Pvkk9bUW8S4AK4cJeDDebnWJNADNCtxCHG',
            'child_count' => 1,
            'children' => ['child_id_456'],
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
            ->assertSee('1 Child Inscription')
            ->assertSee('child_id_456');
    }

    #[Test]
    public function inscription_page_shows_no_content_message_when_no_content_is_present(): void
    {
        $inscriptionId = '4a05eed577b3457348620bafb4340293cfb250777d68ee27a50cf0674141b43ai0';

        $mockData = InscriptionData::from([
            'id' => $inscriptionId,
            'number' => 12345,
            'address' => 'Pvkk9bUW8S4AK4cJeDDebnWJNADNCtxCHG',
            'child_count' => 0,
            'children' => [],
            'content_type' => 'image/png',
            'effective_content_type' => 'image/png',
            'content_length' => null,
            'delegate' => null,
            'fee' => 1000,
            'height' => 100000,
            'value' => 100000,
            'parent_count' => 0,
            'parents' => [],
            'properties' => null,
            'satpoint' => $inscriptionId.':0:0',
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
            ->assertSee('No content');
    }

    #[Test]
    public function inscription_page_returns_404_when_not_found(): void
    {
        $inscriptionId = '0000000000000000000000000000000000000000000000000000000000000000i0';

        $ordinals = Mockery::mock(OrdinalsService::class);
        $ordinals->shouldReceive('getInscription')
            ->once()
            ->with($inscriptionId)
            ->andThrow(new RequestException(
                new \Illuminate\Http\Client\Response(new Response(404))
            ));
        $this->app->instance(OrdinalsService::class, $ordinals);

        $this->get(route('inscription.show', ['inscriptionId' => $inscriptionId]))
            ->assertNotFound();
    }
}
