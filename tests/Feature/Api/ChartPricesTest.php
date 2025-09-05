<?php

namespace Tests\Feature\Api;

use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChartPricesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function chart_prices_requires_signed_url_and_returns_shape(): void
    {
        Price::factory()->usd()->count(10)->create();

        $url = URL::signedRoute('api.chart.prices', [
            'period' => '24h',
            'currency' => 'USD',
        ]);

        $this->get($url)
            ->assertStatus(200)
            ->assertJsonStructure([
                'success', 'series', 'period', 'currency', 'count',
            ])
            ->assertJson([
                'success' => true,
                'period' => '24h',
                'currency' => 'USD',
            ]);
    }
}
