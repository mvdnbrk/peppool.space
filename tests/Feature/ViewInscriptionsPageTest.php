<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Inscription;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewInscriptionsPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[Test]
    public function inscriptions_page_renders_successfully(): void
    {
        $this->get(route('inscription.index'))
            ->assertOk()
            ->assertSee('Inscriptions');
    }

    #[Test]
    public function api_returns_latest_image_and_html_inscriptions(): void
    {
        $image = Inscription::factory()->create(['content_type' => 'image/png']);
        $html = Inscription::factory()->create(['content_type' => 'text/html']);
        $text = Inscription::factory()->create(['content_type' => 'text/plain']);
        $json = Inscription::factory()->create(['content_type' => 'application/json']);

        $response = $this->getJson(route('api.inscriptions.index'))
            ->assertOk()
            ->assertJsonStructure(['inscriptions', 'total', 'current_page', 'last_page']);

        $inscriptionIds = $response->json('inscriptions');

        $this->assertContains($image->inscription_id, $inscriptionIds);
        $this->assertContains($html->inscription_id, $inscriptionIds);
        $this->assertNotContains($text->inscription_id, $inscriptionIds);
        $this->assertNotContains($json->inscription_id, $inscriptionIds);
        $this->assertEquals(2, $response->json('total'));
    }

    #[Test]
    public function api_returns_inscriptions_ordered_by_newest_first(): void
    {
        $older = Inscription::factory()->create(['id' => 1, 'content_type' => 'image/png']);
        $newer = Inscription::factory()->create(['id' => 2, 'content_type' => 'image/png']);

        $response = $this->getJson(route('api.inscriptions.index'))->assertOk();

        $inscriptionIds = $response->json('inscriptions');
        $this->assertSame($newer->inscription_id, $inscriptionIds[0]);
        $this->assertSame($older->inscription_id, $inscriptionIds[1]);
    }

    #[Test]
    public function api_limits_to_60_inscriptions_per_page(): void
    {
        Inscription::factory()->count(65)->create(['content_type' => 'image/png']);

        $response = $this->getJson(route('api.inscriptions.index'))->assertOk();

        $this->assertCount(60, $response->json('inscriptions'));
        $this->assertEquals(65, $response->json('total'));
        $this->assertEquals(1, $response->json('current_page'));
        $this->assertEquals(2, $response->json('last_page'));
    }

    #[Test]
    public function api_returns_empty_when_no_matching_inscriptions(): void
    {
        Inscription::factory()->create(['content_type' => 'text/plain']);

        $this->getJson(route('api.inscriptions.index'))
            ->assertOk()
            ->assertJson(['inscriptions' => [], 'total' => 0]);
    }
}
